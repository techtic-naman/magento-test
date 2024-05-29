<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types = 1);

namespace MageWorx\ReviewAIBase\Api;

interface InvalidateFullPageCacheByProductIdInterface
{
    /**
     * Invalidate the FPC for a specific product by its ID.
     *
     * @param int $productId
     */
    public function invalidateProductFpc(int $productId): void;
}
