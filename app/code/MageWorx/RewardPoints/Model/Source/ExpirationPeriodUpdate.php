<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\RewardPoints\Model\Source;

class ExpirationPeriodUpdate extends \MageWorx\RewardPoints\Model\Source
{
    const UPDATE_NOTHING           = 1;
    const UPDATE_ALL_BALANCES      = 2;
    const UPDATE_NOT_NULL_BALANCES = 3;

    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        return [
            ['label' => __('No'), 'value' => self::UPDATE_NOTHING],
            [
                'label' => __('For balances if expiration date exists'),
                'value' => self::UPDATE_NOT_NULL_BALANCES
            ],
            [
                'label' => __('For all balances'),
                'value' => self::UPDATE_ALL_BALANCES
            ],
        ];
    }
}