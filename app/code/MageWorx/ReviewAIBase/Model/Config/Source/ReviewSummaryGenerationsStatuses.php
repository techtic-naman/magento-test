<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types = 1);

namespace MageWorx\ReviewAIBase\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

class ReviewSummaryGenerationsStatuses implements OptionSourceInterface
{
    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray(): array
    {
        return [
            ['value' => 'generate_review_summary', 'label' => __('Generate Review Summary')]
        ];
    }
}
