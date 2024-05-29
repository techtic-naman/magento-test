<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types = 1);

namespace MageWorx\ReviewAIBase\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Review\Model\Review;
use MageWorx\ReviewAIBase\Api\ReviewSummaryGeneratorInterface;
use MageWorx\ReviewAIBase\Api\ReviewSummaryLoaderInterface;
use MageWorx\ReviewAIBase\Api\ReviewSummaryRepositoryInterface;
use MageWorx\ReviewAIBase\Model\Config\Source\Status;
use Psr\Log\LoggerInterface;

class UpdateQueue implements ObserverInterface
{
    protected ReviewSummaryLoaderInterface     $reviewSummaryLoader;
    protected ReviewSummaryRepositoryInterface $reviewSummaryRepository;
    protected LoggerInterface                  $logger;
    protected ReviewSummaryGeneratorInterface  $reviewSummaryGenerator;

    public function __construct(
        ReviewSummaryLoaderInterface     $reviewSummaryLoader,
        ReviewSummaryRepositoryInterface $reviewSummaryRepository,
        ReviewSummaryGeneratorInterface  $reviewSummaryGenerator,
        LoggerInterface                  $logger
    ) {
        $this->reviewSummaryLoader     = $reviewSummaryLoader;
        $this->reviewSummaryRepository = $reviewSummaryRepository;
        $this->logger                  = $logger;
        $this->reviewSummaryGenerator  = $reviewSummaryGenerator;
    }

    public function execute(Observer $observer): void
    {
        /** @var \Magento\Review\Model\Review $review */
        $review = $observer->getData('object');

        $reviewProductEntityId = $review->getEntityIdByCode(\Magento\Review\Model\Review::ENTITY_PRODUCT_CODE);
        if ($review->getEntityId() !== $reviewProductEntityId) {
            return; // Available only for products
        }

        $productId = (int)$review->getEntityPkValue();
        $storeId   = (int)$review->getStoreId();

        $reviewSummaryEntity = $this->reviewSummaryLoader->getByProductIdAndStoreId($productId, $storeId);
        if (!$reviewSummaryEntity->getIsEnabled()) {
            return; // Available only for enabled products
        }

        if ($review->getId() !== null && (int)$review->getStatusId() === Review::STATUS_APPROVED) {
            // Check review status change
            // If status has been changed from pending to approved add new record
            $reviewSummaryEntity->setStatus(Status::STATUS_PENDING)
                                ->setProductId($productId)
                                ->setStoreId($storeId)
                                ->setUpdateRequired(true);

            $this->reviewSummaryGenerator->addToQueue($productId, $storeId);
        }

        try {
            $this->reviewSummaryRepository->save($reviewSummaryEntity);
        } catch (CouldNotSaveException $couldNotSaveException) {
            $this->logger->warning($couldNotSaveException);
        }
    }
}
