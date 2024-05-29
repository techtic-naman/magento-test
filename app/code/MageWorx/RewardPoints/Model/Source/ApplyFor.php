<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\RewardPoints\Model\Source;


class ApplyFor extends \MageWorx\RewardPoints\Model\Source
{
    const APPLY_FOR_VALUE_SUBTOTAL = 'subtotal';
    const APPLY_FOR_VALUE_SHIPPING = 'shipping';
    const APPLY_FOR_VALUE_TAX      = 'tax';

    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['label' => __('Subtotal'), 'value' => static::APPLY_FOR_VALUE_SUBTOTAL],
            ['label' => __('Shipping'), 'value' => static::APPLY_FOR_VALUE_SHIPPING],
            ['label' => __('Tax'), 'value' => static::APPLY_FOR_VALUE_TAX]
        ];
    }
}