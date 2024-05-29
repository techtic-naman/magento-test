<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace MageWorx\CountdownTimersBase\Model\Source\CountdownTimer;

class Status extends \MageWorx\CountdownTimersBase\Model\Source
{
    const DISABLE = 1;
    const ENABLE  = 2;

    /**
     * To option array
     *
     * @return array
     */
    public function toOptionArray(): array
    {
        $options = [
            [
                'value' => self::DISABLE,
                'label' => __('Disable')
            ],
            [
                'value' => self::ENABLE,
                'label' => __('Enable')
            ]
        ];

        return $options;
    }
}
