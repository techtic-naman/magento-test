<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types = 1);

namespace MageWorx\ReviewAIBase\Model\AdditionalDataProcessor;

use Magento\Review\Model\Review;
use MageWorx\ReviewAIBase\Api\AdditionalDataProcessorInterface;

class IsRecommended implements AdditionalDataProcessorInterface
{
    /**
     * @inheritDoc
     */
    public function process(Review $review): ?string
    {
        $value = $review->getData('is_recommend');
        if ($value !== null) {
            return $value ? 'Yes' : 'No';
        }

        return 'Not Defined';
    }
}
