<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace MageWorx\SocialProofBase\Model\Source\Campaign\Categories;

class AssignType extends \MageWorx\SocialProofBase\Model\Source
{
    const ALL_CATEGORIES      = 1;
    const SPECIFIC_CATEGORIES = 2;

    /**
     * to option array
     *
     * @return array
     */
    public function toOptionArray(): array
    {
        return [
            [
                'value' => self::ALL_CATEGORIES,
                'label' => __('All categories')
            ],
            [
                'value' => self::SPECIFIC_CATEGORIES,
                'label' => __('Specific categories')
            ]
        ];
    }
}
