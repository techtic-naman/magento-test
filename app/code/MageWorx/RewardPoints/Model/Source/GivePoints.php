<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\RewardPoints\Model\Source;

use MageWorx\RewardPoints\Model\Rule;

/**
 * Class GivePoints
 *
 * simple_action
 */
class GivePoints extends \MageWorx\RewardPoints\Model\Source
{
    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        return [
            ['label' => __('Get X Points'), 'value' => Rule::CALCULATION_METHOD_FIXED],
            [
                'label' => __('Get X Points for every Y spent'),
                'value' => Rule::CALCULATION_METHOD_SPEND_Y_GET_X
            ],
            [
                'label' => __('Get X Points for every Y spent starting from Z spend'),
                'value' => Rule::CALCULATION_METHOD_SPEND_Y_MORE_THAN_Z_GET_X
            ],
            [
                'label' => __('Get X Points for every Y quantity'),
                'value' => Rule::CALCULATION_METHOD_BUY_Y_GET_X
            ],
            [
                'label' => __('Get X Points for every Y quantity starting from Z quantity'),
                'value' => Rule::CALCULATION_METHOD_BUY_Y_MORE_THAN_Z_GET_X
            ]
        ];
    }
}