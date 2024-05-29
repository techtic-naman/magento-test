<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace MageWorx\SocialProofBase\Model\Source\Campaign;

class DisplayMode extends \MageWorx\SocialProofBase\Model\Source
{
    const POPUP     = 'popup';
    const HTML_TEXT = 'html-text';

    /**
     * To option array
     *
     * @return array
     */
    public function toOptionArray(): array
    {
        return [
            [
                'value' => self::POPUP,
                'label' => __('Pop-up')
            ],
            [
                'value' => self::HTML_TEXT,
                'label' => __('HTML text')
            ]
        ];
    }
}
