<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace MageWorx\SocialProofBase\Block\Adminhtml\Campaign\Edit\Buttons;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;
use MageWorx\SocialProofBase\Api\Data\CampaignInterface;
use Magento\Framework\Exception\LocalizedException;

class Delete extends Generic implements ButtonProviderInterface
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
            'label'      => __('Delete Campaign'),
            'class'      => 'delete',
            'on_click'   => 'deleteConfirm(\'' . __('Are you sure you want to do this?') . '\', \'' .
                $this->getDeleteUrl() . '\')',
            'sort_order' => 20,
        ];
    }

    /**
     * @return string
     * @throws LocalizedException
     */
    public function getDeleteUrl(): string
    {
        return $this->getUrl('*/*/delete', [CampaignInterface::CAMPAIGN_ID => $this->getCampaignId()]);
    }
}
