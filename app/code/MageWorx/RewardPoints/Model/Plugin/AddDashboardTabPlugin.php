<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\RewardPoints\Model\Plugin;

class AddDashboardTabPlugin
{
    /**
     * @var \MageWorx\RewardPoints\Helper\Data
     */
    protected $helperData;

    /**
     * AddDashboardTabPlugin constructor.
     *
     * @param \MageWorx\RewardPoints\Helper\Data $helperData
     */
    public function __construct(
        \MageWorx\RewardPoints\Helper\Data $helperData
    ) {
        $this->helperData = $helperData;
    }

    /**
     * Add tab to dashboard
     *
     * @param \Magento\Backend\Block\Dashboard\Grids $subject
     * @param \Magento\Backend\Block\Dashboard\Grids $result
     * @return \Magento\Backend\Block\Dashboard\Grids
     */
    public function afterSetLayout($subject, $result)
    {

        if ($this->helperData->isEnable()) {

            $result->addTab(
                'rewardpoints',
                [
                    'label' => __('Reward Points'),
                    'url'   => $result->getUrl('mageworx_rewardpoints/dashboard/statistics', ['_current' => true]),
                    'class' => 'ajax'
                ]
            );
        }

        return $result;
    }
}