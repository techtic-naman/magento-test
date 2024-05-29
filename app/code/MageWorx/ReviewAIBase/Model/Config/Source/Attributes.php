<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types = 1);

namespace MageWorx\ReviewAIBase\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

class Attributes implements OptionSourceInterface
{
    /**
     * Get options as an array
     *
     * @return array
     */
    public function toOptionArray(): array
    {
        return [
            ['value' => 'location_country', 'label' => __('Location country')],
            ['value' => 'location_state', 'label' => __('Location state/region')],
            ['value' => 'verified_customer', 'label' => __('Verified customer')],
            ['value' => 'helpful', 'label' => __('Helpful')],
            ['value' => 'recommended', 'label' => __('Recommended')],
            ['value' => 'pros_cons', 'label' => __('Pros & Cons')],
        ];
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'location_country' => __('Location country'),
            'location_state' => __('Location state/region'),
            'verified_customer' => __('Verified customer'),
            'helpful' => __('Helpful'),
            'recommended' => __('Recommended'),
            'pros_cons' => __('Pros & Cons'),
        ];
    }
}
