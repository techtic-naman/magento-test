<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\RewardPoints\Model\Source;

use MageWorx\RewardPoints\Model\Rule;

class CalculationTypes extends \MageWorx\RewardPoints\Model\Source
{
    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        return [
            [
                'label' => __('Fixed'),
                'value' => Rule::CALCULATION_TYPE_FIXED
            ],
            [
                'label' => __('Percent'),
                'value' => Rule::CALCULATION_TYPE_PERCENT
            ],
        ];
    }
}