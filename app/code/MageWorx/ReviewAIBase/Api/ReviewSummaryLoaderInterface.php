<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types = 1);

namespace MageWorx\ReviewAIBase\Api;

use MageWorx\ReviewAIBase\Api\Data\ReviewSummaryInterface;
use Magento\Framework\Exception\NoSuchEntityException;

interface ReviewSummaryLoaderInterface
{
    /**
     * Get new or existing Review Summary entity by provided product id and store id.
     *
     * @param int $productId
     * @param int $storeId
     * @return ReviewSummaryInterface
     */
    public function getByProductIdAndStoreId(int $productId, int $storeId): ReviewSummaryInterface;

    /**
     * Get existing Review Summary entity by queue.
     *
     * @return ReviewSummaryInterface
     * @throws NoSuchEntityException
     */
    public function getByQueue(): ReviewSummaryInterface;
}
