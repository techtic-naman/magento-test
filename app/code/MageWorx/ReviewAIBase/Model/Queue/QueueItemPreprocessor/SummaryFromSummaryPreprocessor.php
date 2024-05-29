<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types = 1);

namespace MageWorx\ReviewAIBase\Model\Queue\QueueItemPreprocessor;

use Magento\Framework\Api\SearchCriteriaBuilder;
use MageWorx\OpenAI\Api\Data\QueueItemInterface;
use MageWorx\OpenAI\Api\QueueItemPreprocessorInterface;
use MageWorx\OpenAI\Api\QueueRepositoryInterface;
use MageWorx\OpenAI\Exception\QueueItemPreprocessingException;
use MageWorx\OpenAI\Model\ResourceModel\QueueItem;

class SummaryFromSummaryPreprocessor implements QueueItemPreprocessorInterface
{
    private QueueItem                $queueItemResource;
    private QueueRepositoryInterface $queueItemRepository;
    private SearchCriteriaBuilder    $searchCriteriaBuilder;

    public function __construct(
        QueueItem                $queueItemResource,
        QueueRepositoryInterface $queueItemRepository,
        SearchCriteriaBuilder    $searchCriteriaBuilder
    ) {
        $this->queueItemResource     = $queueItemResource;
        $this->queueItemRepository   = $queueItemRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    /**
     * @inheritDoc
     */
    public function execute(QueueItemInterface $queueItem): QueueItemInterface
    {
        // Get all queue item dependencies
        $dependenciesIds = $this->queueItemResource->getDependenciesIds($queueItem->getId());
        if (empty($dependenciesIds)) {
            throw new QueueItemPreprocessingException(
                __('Queue item with id %1 has no dependencies', $queueItem->getId())
            );
        }

        // Create search criteria for dependent queue items
        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter($this->queueItemResource->getIdFieldName(), $dependenciesIds, 'in')
            ->create();

        // Load all dependent queue items using repository
        $list  = $this->queueItemRepository->getList($searchCriteria);
        $items = $list->getItems();

        if ($list->getTotalCount() < 1) {
            throw new QueueItemPreprocessingException(
                __('Unable to load dependencies for queue item with id %1', $queueItem->getId())
            );
        }

        // Get its summary and Summarize content in array as context for future request
        $context = [];
        /** @var QueueItemInterface $item */
        foreach ($items as $item) {
            $itemResponse    = $item->getResponse();
            $responseContent = $itemResponse->getContent();
            if ($responseContent) { // Skip empty response
                $context[] = $responseContent;
            }
        }

        // Update context in queue item before request.
        // Later when items saved with updated status new context will be saved in db too.
        $queueItem->setContext($context);

        return $queueItem;
    }
}
