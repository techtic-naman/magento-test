<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace MageWorx\SocialProofBase\Block\Adminhtml\Campaign\Edit\Buttons;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;
use Magento\Framework\Exception\LocalizedException;

class SaveAndContinue extends Generic implements ButtonProviderInterface
{
    /**
     * get button data
     *
     * @return array
     * @throws LocalizedException
     */
    public function getButtonData()
    {
        if (!$this->getCampaignId()) {
            return [];
        }

        return [
            'label'          => __('Save and Continue Edit'),
            'class'          => 'save',
            'data_attribute' => [
                'mage-init' => [
                    'button' => ['event' => 'saveAndContinueEdit'],
                ],
            ],
            'sort_order'     => 80,
        ];
    }
}
