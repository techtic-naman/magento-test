<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types = 1);

namespace MageWorx\ReviewAIBase\Model;

use Magento\Framework\Api\SearchCriteriaBuilderFactory;
use Magento\Framework\Exception\NoSuchEntityException;
use MageWorx\ReviewAIBase\Api\Data\ReviewSummaryInterface;
use MageWorx\ReviewAIBase\Api\Data\ReviewSummaryInterfaceFactory;
use MageWorx\ReviewAIBase\Api\ReviewSummaryLoaderInterface;
use MageWorx\ReviewAIBase\Api\ReviewSummaryRepositoryInterface;
use MageWorx\ReviewAIBase\Model\Config\Source\Status;
use Psr\Log\LoggerInterface;

class ReviewSummaryLoader implements ReviewSummaryLoaderInterface
{
    protected ReviewSummaryRepositoryInterface $reviewSummaryRepository;
    protected LoggerInterface                  $logger;
    protected SearchCriteriaBuilderFactory     $searchCriteriaBuilderFactory;
    protected ReviewSummaryInterfaceFactory    $reviewSummaryFactory;

    public function __construct(
        ReviewSummaryRepositoryInterface $reviewSummaryRepository,
        SearchCriteriaBuilderFactory     $searchCriteriaBuilderFactory,
        ReviewSummaryInterfaceFactory    $reviewSummaryFactory,
        LoggerInterface                  $logger
    ) {
        $this->reviewSummaryRepository      = $reviewSummaryRepository;
        $this->searchCriteriaBuilderFactory = $searchCriteriaBuilderFactory;
        $this->reviewSummaryFactory         = $reviewSummaryFactory;
        $this->logger                       = $logger;
    }

    /**
     * @inheritDoc
     */
    public function getByProductIdAndStoreId(int $productId, int $storeId): ReviewSummaryInterface
    {
        $searchCriteriaBuilder = $this->searchCriteriaBuilderFactory->create();
        $searchCriteria        = $searchCriteriaBuilder->addFilter('product_id', $productId)
                                                       ->addFilter('store_id', $storeId)
                                                       ->setPageSize(1)
                                                       ->setCurrentPage(1)
                                                       ->create();

        $reviewSummaryList = $this->reviewSummaryRepository->getList($searchCriteria);
        if ($reviewSummaryList->getTotalCount() < 1) {
            /** @var ReviewSummaryInterface $reviewSummaryEntity */
            $reviewSummaryEntity = $this->reviewSummaryFactory->create();
            $reviewSummaryEntity->setProductId($productId)
                                ->setStoreId($storeId);
        } else {
            $items               = $reviewSummaryList->getItems();
            $reviewSummaryEntity = reset($items);
        }

        return $reviewSummaryEntity;
    }

    /**
     * @inheritDoc
     */
    public function getByQueue(): ReviewSummaryInterface
    {
        $searchCriteriaBuilder = $this->searchCriteriaBuilderFactory->create();
        $searchCriteria        = $searchCriteriaBuilder->addFilter('update_required', true)
                                                       ->addFilter('is_enabled', true)
                                                       ->addFilter('status', Status::STATUS_PENDING)
                                                       ->setPageSize(1)
                                                       ->setCurrentPage(1)
                                                       ->create();

        $reviewSummaryList = $this->reviewSummaryRepository->getList($searchCriteria);
        if ($reviewSummaryList->getTotalCount() < 1) {
            throw new NoSuchEntityException(__('Empty queue'));
        } else {
            $items               = $reviewSummaryList->getItems();
            $reviewSummaryEntity = reset($items);
        }

        return $reviewSummaryEntity;
    }
}
