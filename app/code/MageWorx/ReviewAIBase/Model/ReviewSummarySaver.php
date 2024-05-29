<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types = 1);

namespace MageWorx\ReviewAIBase\Model;

use Magento\Framework\Exception\CouldNotSaveException;
use MageWorx\ReviewAIBase\Api\Data\ReviewSummaryInterface;
use MageWorx\ReviewAIBase\Api\InvalidateFullPageCacheByProductIdInterface as CacheInvalidator;
use MageWorx\ReviewAIBase\Api\ReviewSummaryLoaderInterface;
use MageWorx\ReviewAIBase\Api\ReviewSummarySaverInterface;
use Magento\Framework\Api\SearchCriteriaBuilderFactory;
use MageWorx\ReviewAIBase\Api\Data\ReviewSummaryInterfaceFactory;
use MageWorx\ReviewAIBase\Api\ReviewSummaryRepositoryInterface;
use MageWorx\ReviewAIBase\Helper\Config;
use MageWorx\ReviewAIBase\Model\Config\Source\Status;
use Psr\Log\LoggerInterface;

class ReviewSummarySaver implements ReviewSummarySaverInterface
{
    protected ReviewSummaryRepositoryInterface $reviewSummaryRepository;
    protected LoggerInterface                  $logger;
    protected SearchCriteriaBuilderFactory     $searchCriteriaBuilderFactory;
    protected ReviewSummaryInterfaceFactory    $reviewSummaryFactory;
    protected ReviewSummaryLoaderInterface     $reviewSummaryLoader;
    protected CacheInvalidator                 $cacheInvalidator;
    protected Config                           $config;

    public function __construct(
        ReviewSummaryLoaderInterface     $reviewSummaryLoader,
        ReviewSummaryRepositoryInterface $reviewSummaryRepository,
        SearchCriteriaBuilderFactory     $searchCriteriaBuilderFactory,
        ReviewSummaryInterfaceFactory    $reviewSummaryFactory,
        CacheInvalidator                 $invalidateFullPageCacheByProductId,
        Config                           $config,
        LoggerInterface                  $logger
    ) {
        $this->reviewSummaryLoader          = $reviewSummaryLoader;
        $this->reviewSummaryRepository      = $reviewSummaryRepository;
        $this->searchCriteriaBuilderFactory = $searchCriteriaBuilderFactory;
        $this->reviewSummaryFactory         = $reviewSummaryFactory;
        $this->cacheInvalidator             = $invalidateFullPageCacheByProductId;
        $this->config                       = $config;
        $this->logger                       = $logger;
    }

    /**
     * @inheritDoc
     */
    public function saveUpdate(string $content, int $productId, int $storeId): ReviewSummaryInterface
    {
        $reviewSummaryEntity = $this->reviewSummaryLoader->getByProductIdAndStoreId($productId, $storeId);
        $reviewSummaryEntity->setProductId($productId)
                            ->setStoreId($storeId)
                            ->setSummaryData($content)
                            ->setStatus(Status::STATUS_READY);

        try {
            $reviewSummaryEntity = $this->reviewSummaryRepository->save($reviewSummaryEntity);
        } catch (CouldNotSaveException $exception) {
            $this->logger->error($exception->getMessage(), [$exception->getTraceAsString()]);
            throw $exception;
        }

        if (!$this->config->isXReviewEnabled()) {
            // Invalidate full page cache for product in case XReview is disabled or not installed
            $this->cacheInvalidator->invalidateProductFpc($productId);
        }

        return $reviewSummaryEntity;
    }
}
