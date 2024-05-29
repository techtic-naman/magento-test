<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types = 1);

namespace MageWorx\ReviewAIBase\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use MageWorx\ReviewAIBase\Api\ReviewSummarySaverInterface;
use Magento\Catalog\Api\Data\ProductInterface;

class UpdateReviewSummaryOnProductSaveAfter implements ObserverInterface
{
    protected ReviewSummarySaverInterface $reviewSummarySaver;

    /**
     * @param ReviewSummarySaverInterface $reviewSummarySaver
     */
    public function __construct(ReviewSummarySaverInterface $reviewSummarySaver)
    {
        $this->reviewSummarySaver = $reviewSummarySaver;
    }

    /**
     * Execute observer
     *
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        /** @var ProductInterface $product */
        $product = $observer->getEvent()->getEntity();

        if (!$product instanceof \Magento\Catalog\Api\Data\ProductInterface) {
            return;
        }

        $originalReviewSummary = (string)$product->getOrigData('summary_data');
        $currentReviewSummary  = (string)$product->getData('summary_data');
        $storeId               = (int)$product->getStoreId();
        $productId             = (int)$product->getId();

        if ($originalReviewSummary !== $currentReviewSummary) {
            $this->reviewSummarySaver->saveUpdate($currentReviewSummary, $productId, $storeId);
        }
    }
}
