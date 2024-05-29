<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace MageWorx\SocialProofBase\Model\Source\Campaign\Products;

class AssignType extends \MageWorx\SocialProofBase\Model\Source
{
    const ALL_PRODUCTS      = 1;
    const SPECIFIC_PRODUCTS = 2;
    const BY_CONDITIONS     = 3;

    /**
     * to option array
     *
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
                'value' => self::BY_CONDITIONS,
                'label' => __('By conditions')
            ]
        ];
    }
}
