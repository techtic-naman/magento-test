<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types = 1);

namespace MageWorx\ReviewAIBase\Model\AdditionalDataProcessor;

use Magento\Review\Model\Review;
use MageWorx\ReviewAIBase\Api\AdditionalDataProcessorInterface;

class LocationState implements AdditionalDataProcessorInterface
{
    /**
     * @inheritDoc
     */
    public function process(Review $review): ?string
    {
        $value = $review->getData('region');
        if ($value) {
            return $value;
        }

        return 'Not Defined';
    }
}
