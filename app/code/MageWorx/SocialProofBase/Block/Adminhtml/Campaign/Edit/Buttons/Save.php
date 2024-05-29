<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace MageWorx\SocialProofBase\Block\Adminhtml\Campaign\Edit\Buttons;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;
use Magento\Framework\Exception\LocalizedException;

class Save extends Generic implements ButtonProviderInterface
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
            'label'          => __('Save Campaign'),
            'class'          => 'save primary',
            'data_attribute' => [
                'mage-init' => ['button' => ['event' => 'save']],
                'form-role' => 'save',
            ],
            'sort_order'     => 90,
        ];
    }
}
