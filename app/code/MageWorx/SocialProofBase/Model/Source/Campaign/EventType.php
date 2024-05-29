<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace MageWorx\SocialProofBase\Model\Source\Campaign;

class EventType extends \MageWorx\SocialProofBase\Model\Source
{
    const RECENT_SALES = 'recent-sales';
    const VIEWS        = 'views';

    /**
     * To option array
     *
     * @return array
     */
    public function toOptionArray(): array
    {
        return [
            [
                'value' => self::RECENT_SALES,
                'label' => __('Recent sales')
            ],
            [
                'value' => self::VIEWS,
                'label' => __('Views (Popularity)')
            ]
        ];
    }
}
