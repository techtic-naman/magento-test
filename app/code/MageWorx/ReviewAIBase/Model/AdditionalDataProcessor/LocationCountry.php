<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types = 1);

namespace MageWorx\ReviewAIBase\Model\AdditionalDataProcessor;

use Magento\Review\Model\Review;
use MageWorx\ReviewAIBase\Api\AdditionalDataProcessorInterface;

class LocationCountry implements AdditionalDataProcessorInterface
{
    /**
     * @inheritDoc
     */
    public function process(Review $review): ?string
    {
        $locationCode = $review->getData('location');
        if ($locationCode) {
            return $locationCode;
        }

        return 'Not Defined';
    }
}
