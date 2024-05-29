<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace MageWorx\SocialProofBase\Model\Source\Campaign\CmsPages;

class AssignType extends \MageWorx\SocialProofBase\Model\Source
{
    const ALL_PAGES      = 1;
    const SPECIFIC_PAGES = 2;

    /**
     * to option array
     *
     * @return array
     */
    public function toOptionArray(): array
    {
        return [
            [
                'value' => self::ALL_PAGES,
                'label' => __('All pages')
            ],
            [
                'value' => self::SPECIFIC_PAGES,
                'label' => __('Specific pages')
            ]
        ];
    }
}
