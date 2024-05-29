<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace MageWorx\SocialProofBase\Model\Source\Campaign;

class Position extends \MageWorx\SocialProofBase\Model\Source
{
    const TOP_RIGHT    = 'top-right';
    const TOP_LEFT     = 'top-left';
    const BOTTOM_LEFT  = 'bottom-left';
    const BOTTOM_RIGHT = 'bottom-right';

    /**
     * To option array
     *
     * @return array
     */
    public function toOptionArray(): array
    {
        return [
            [
                'value' => self::TOP_RIGHT,
                'label' => __('Top Right')
            ],
            [
                'value' => self::TOP_LEFT,
                'label' => __('Top Left')
            ],
            [
                'value' => self::BOTTOM_LEFT,
                'label' => __('Bottom Left')
            ],
            [
                'value' => self::BOTTOM_RIGHT,
                'label' => __('Bottom Right')
            ]
        ];
    }
}
