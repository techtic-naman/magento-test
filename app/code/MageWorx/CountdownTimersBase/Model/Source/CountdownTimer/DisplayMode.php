<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace MageWorx\CountdownTimersBase\Model\Source\CountdownTimer;

class DisplayMode extends \MageWorx\CountdownTimersBase\Model\Source
{
    const ALL_PRODUCTS      = 'all-products';
    const SPECIFIC_PRODUCTS = 'specific-products';
    const CUSTOM            = 'custom';

    /**
     * @return array
     */
    public function toOptionArray(): array
    {
        return [
            [
                'value' => self::ALL_PRODUCTS,
                'label' => __('All products')
            ],
            [
                'value' => self::SPECIFIC_PRODUCTS,
                'label' => __('Specific products')
            ],
            [
                'value' => self::CUSTOM,
                'label' => __('Custom')
            ]
        ];
    }
}
