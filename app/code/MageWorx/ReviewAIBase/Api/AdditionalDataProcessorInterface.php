<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types = 1);

namespace MageWorx\ReviewAIBase\Api;

use Magento\Review\Model\Review;

interface AdditionalDataProcessorInterface
{
    /**
     * Get additional data for review prompt.
     *
     * @param Review $review
     * @return string|null
     */
    public function process(Review $review): ?string;
}
