<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace MageWorx\SocialProofBase\Block\Adminhtml\Campaign\Edit\Buttons;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;
use Magento\Framework\Exception\LocalizedException;

class Reset extends Generic implements ButtonProviderInterface
{
    /**
     * get button data
     *
     * @return array
     * @throws LocalizedException
     */
    public function getButtonData(): array
    {
        if (!$this->getCampaignId()) {
            return [];
        }

        return [
            'label'      => __('Reset'),
            'class'      => 'reset',
            'on_click'   => 'location.reload();',
            'sort_order' => 30
        ];
    }
}
