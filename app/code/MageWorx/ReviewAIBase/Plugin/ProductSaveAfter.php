<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types = 1);

namespace MageWorx\ReviewAIBase\Plugin;

use Magento\Catalog\Model\ProductRepository;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use MageWorx\ReviewAIBase\Api\ReviewSummarySaverInterface;

/**
 * ProductSaveAfter Plugin
 *
 * This plugin intercepts the saving of a product in the Magento system.
 * If the 'review_summary' data of the product has been altered during the save process,
 * the changes will be saved or updated in the ReviewSummary entity.
 *
 * Usage:
 * The plugin listens to the `afterSave` method of the ProductRepository.
 * When a product is saved, it checks for modifications in the 'review_summary' field.
 * If changes are detected, they get saved or updated using the ReviewSummarySaver service.
 */
class ProductSaveAfter
{
    protected ReviewSummarySaverInterface $reviewSummarySaver;

    public function __construct(
        ReviewSummarySaverInterface $reviewSummarySaver
    ) {
        $this->reviewSummarySaver = $reviewSummarySaver;
    }

    /**
     * @param ProductRepository $subject
     * @param ProductInterface $resultProduct
     * @return ProductInterface
     * @throws CouldNotSaveException
     */
    public function afterSave(ProductRepository $subject, ProductInterface $resultProduct): ProductInterface
    {
        $originalReviewSummary = (string)$resultProduct->getOrigData('summary_data');
        $currentReviewSummary  = (string)$resultProduct->getData('summary_data');
        $storeId               = (int)$resultProduct->getStoreId();
        $productId             = (int)$resultProduct->getId();

        if ($originalReviewSummary !== $currentReviewSummary) {
            $this->reviewSummarySaver->saveUpdate($currentReviewSummary, $productId, $storeId);
        }

        return $resultProduct;
    }
}
