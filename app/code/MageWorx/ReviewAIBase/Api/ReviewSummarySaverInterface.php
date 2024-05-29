<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types = 1);

namespace MageWorx\ReviewAIBase\Api;

use MageWorx\ReviewAIBase\Api\Data\ReviewSummaryInterface;

interface ReviewSummarySaverInterface
{
    /**
     * Save new or update existing Review Summary entity.
     *
     * @param string $content
     * @param int $productId
     * @param int $storeId
     * @return ReviewSummaryInterface
     */
    public function saveUpdate(string $content, int $productId, int $storeId): ReviewSummaryInterface;
}
