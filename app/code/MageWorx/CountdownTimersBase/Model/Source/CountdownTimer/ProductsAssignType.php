<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace MageWorx\CountdownTimersBase\Model\Source\CountdownTimer;

class ProductsAssignType extends \MageWorx\CountdownTimersBase\Model\Source
{
    const SPECIFIC_PRODUCTS = 1;
    const BY_CONDITIONS     = 2;

    /**
     * to option array
     *
     * @return array
     */
    public function toOptionArray(): array
    {
        $options = [
            [
                'value' => self::SPECIFIC_PRODUCTS,
                'label' => __('Specific products')
            ],
            [
                'value' => self::BY_CONDITIONS,
                'label' => __('By conditions')
            ]
        ];

        return $options;
    }
}
