<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types = 1);

namespace MageWorx\ReviewAIBase\Api;

interface DisplayReviewSummaryValidatorInterface
{
    /**
     * Is need to display review summary for product
     *
     * @param int $productId
     * @return bool
     */
    public function validate(int $productId): bool;
}
