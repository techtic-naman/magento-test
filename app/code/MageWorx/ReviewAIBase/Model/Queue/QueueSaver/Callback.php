<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types = 1);

namespace MageWorx\ReviewAIBase\Model\Queue\QueueSaver;

use MageWorx\OpenAI\Api\CallbackInterface;
use MageWorx\OpenAI\Api\OptionsInterface;
use MageWorx\OpenAI\Api\ResponseInterface;
use MageWorx\OpenAI\Exception\CallbackProcessingException;
use MageWorx\ReviewAIBase\Api\ReviewSummarySaverInterface;
use Psr\Log\LoggerInterface;

class Callback implements CallbackInterface
{
    protected LoggerInterface             $logger;
    protected ReviewSummarySaverInterface $reviewSummarySaver;

    public function __construct(
        ReviewSummarySaverInterface $reviewSummarySaver,
        LoggerInterface             $logger
    ) {
        $this->logger             = $logger;
        $this->reviewSummarySaver = $reviewSummarySaver;
    }

    public function execute(
        OptionsInterface  $options,
        ResponseInterface $response,
        ?array            $additionalData = []
    ): void {
        $productId = isset($additionalData['product_id']) ? (int)$additionalData['product_id'] : null;
        $storeId   = isset($additionalData['store_id']) ? (int)$additionalData['store_id'] : null;

        if ($productId === null || $storeId === null) {
            $this->logger->error('Callback data is invalid', $additionalData);
            throw new CallbackProcessingException(__('Callback data is invalid'));
        }

        $content = $this->unpackResponseContent($response);

        $this->reviewSummarySaver->saveUpdate($content, $productId, $storeId);
    }

    public function unpackResponseContent(ResponseInterface $response): string
    {
        $chatResponse = $response->getChatResponse();
        if (!empty($chatResponse['error'])) {
            $error =
                is_string($chatResponse['error']) ? $chatResponse['error'] : json_encode($chatResponse['error']);
            $this->logger->warning($error);

            throw new CallbackProcessingException(__('Callback data has error'));
        }

        return $response->getContent();
    }
}
