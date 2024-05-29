<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types = 1);

namespace MageWorx\ReviewAIBase\Model\Queue\QueueItemErrorHandler;

use Magento\Framework\Exception\LocalizedException;
use MageWorx\OpenAI\Api\Data\QueueItemInterface;
use MageWorx\OpenAI\Api\GeneralOpenAIHelperInterface;
use MageWorx\OpenAI\Api\QueueItemErrorHandlerInterface;
use MageWorx\OpenAI\Api\QueueManagementInterface;
use MageWorx\OpenAI\Api\QueueProcessManagementInterface;
use MageWorx\OpenAI\Api\QueueRepositoryInterface;
use MageWorx\OpenAI\Model\ResourceModel\QueueItem as QueueItemResource;
use MageWorx\ReviewAIBase\Helper\Config as ConfigHelper;

class ContextLengthExceededHandler implements QueueItemErrorHandlerInterface
{
    const ERROR_TYPE = 'invalid_request_error';
    const ERROR_CODE = 'context_length_exceeded';

    protected GeneralOpenAIHelperInterface    $generalHelper;
    protected QueueManagementInterface        $queueManagement;
    protected QueueProcessManagementInterface $queueProcessManagement;
    protected QueueRepositoryInterface        $queueRepository;
    protected QueueItemResource               $queueItemResource;
    protected ConfigHelper                    $config;

    public function __construct(
        GeneralOpenAIHelperInterface    $generalHelper,
        QueueManagementInterface        $queueManagement,
        QueueProcessManagementInterface $queueProcessManagement,
        QueueRepositoryInterface        $queueRepository,
        QueueItemResource               $queueItemResource,
        ConfigHelper                    $config
    ) {

        $this->generalHelper          = $generalHelper;
        $this->queueManagement        = $queueManagement;
        $this->queueProcessManagement = $queueProcessManagement;
        $this->queueRepository        = $queueRepository;
        $this->queueItemResource      = $queueItemResource;
        $this->config                 = $config;
    }

    /**
     * @inheritDoc
     */
    public function execute(QueueItemInterface $queueItem): bool
    {
        $response = $queueItem->getResponse();
        if (!$response->getIsError()) {
            return false;
        }

        $content = $response->getChatResponse();
        if (empty($content['error'])) {
            return false;
        }

        if ($content['error']['type'] === static::ERROR_TYPE && $content['error']['code'] === static::ERROR_CODE) {
            // Force change status of queue item to restrict access to it from the queue processor
            $this->queueItemResource->updateStatus($queueItem->getId(), QueueItemInterface::STATUS_PROCESSING);

            /**
             * Split context to parts
             * Create new queue items for each part
             * Create new summary queue item
             * Mark existing queue item as unprocessable error
             * Proceed queue
             */
            $context        = $queueItem->getContext();
            $content        = $queueItem->getContent();
            $additionalData = $queueItem->getAdditionalData();
            $productId      = $additionalData['product_id'];
            $storeId        = $additionalData['store_id'];
            $options        = $queueItem->getOptions();
            $callback       = 'review_ai_summary';

            $processCode   = 'mageworx-reviewai-generate-review-summary';
            $processType   = 'generate-review-summary';
            $processName   = 'Generate Review Summary';
            $processModule = 'mageworx_reviewai';

            $contentLength           = $this->generalHelper->calculateStringLengthInTokens($content);
            $contextLength           = $this->generalHelper->calculateContextLength($context);
            $fullLength              = $contentLength + $contextLength;
            $contextMaxAllowedLength = $this->generalHelper->getMaxModelSupportedContextLength($options->getModel());

            // Split to parts, generate summary for each one and summarize all in final request
            $parts   = $this->generalHelper->splitRequestToParts($content, $context, $contextMaxAllowedLength);
            // @TODO: Update after OpenAI 1.2.0 release
            $process = $this->queueProcessManagement->registerProcess($processName, count($parts) + 1);
            foreach ($parts as $part) {
                // Add parts on which summary depends
                $queueItems[] = $this->queueManagement->addToQueue(
                    $content,
                    $options,
                    null, // No callback to prevent from processing by the callback queue processor
                    $part,
                    $process,
                    $additionalData
                );
            }

            if (empty($queueItems)) {
                // Return back queue item status to allow it to be processed by queue processor one more time
                $this->queueItemResource->updateStatus($queueItem->getId(), QueueItemInterface::STATUS_FAILED);
                throw new LocalizedException(__('Unable to create queue items for context length exceeded error'));
            }

            // Add summary queue item
            $summaryContent = $this->config->getSummarizeMessage($storeId);
            // We will use ID for creation of dependency records
            $summaryQueueItem = $this->queueManagement->addToQueue(
                $summaryContent,
                $options,
                $callback,
                [], // No context, because we will use result of another requests when them will be ready
                $process,
                $additionalData,
                'review_ai_summary_from_summary',
                'review_ai_summary'
            );

            // Add dependency records
            $this->queueManagement->addDependency($summaryQueueItem, $queueItems);

            // Set original queue item as completed to prevent it from processing by queue
            $this->queueItemResource->updateStatus($queueItem->getId(), QueueItemInterface::STATUS_COMPLETED);

            return true;
        }

        // Error of this type can not be processed using this error handler
        return false;
    }
}
