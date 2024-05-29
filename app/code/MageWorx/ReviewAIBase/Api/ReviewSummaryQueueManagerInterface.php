<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types = 1);

namespace MageWorx\ReviewAIBase\Api;

interface ReviewSummaryQueueManagerInterface
{

    /**
     * Add reviews of product to the update queue.
     *
     * @param int $productId
     * @param int $storeId
     * @return void
     */
    public function addReviewToQueue(
        int $productId,
        int $storeId
    ): void;
}
