<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\RewardPoints\Block;

use Magento\Checkout\Block\Checkout\LayoutProcessorInterface;

/**
 * Class LayoutProcessor
 */
class LayoutProcessor implements LayoutProcessorInterface
{
    /**
     * @var \MageWorx\RewardPoints\Helper\Data
     */
    protected $helperData;

    /**
     * LayoutProcessor constructor.
     *
     * @param \MageWorx\RewardPoints\Helper\Data $helperData
     */
    public function __construct(
        \MageWorx\RewardPoints\Helper\Data $helperData
    ) {
        $this->helperData = $helperData;
    }

    /**
     * @param array $jsLayout
     * @return array
     */
    public function process($jsLayout)
    {
        if (isset(
            $jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']['payment']
            ['children']['afterMethods']['children']['mageworx_rewardpoints']
        )) {
            $component = &$jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']['payment']
            ['children']['afterMethods']['children']['mageworx_rewardpoints'];

            $component['config']['template'] = $this->getJsTemplate();
            $component['inputPlaceholder']   = $this->helperData->getCustomPointsInputPlaceholder();
        }

        return $jsLayout;
    }

    /**
     * @return string
     */
    public function getJsTemplate()
    {
        if ($this->helperData->isAllowedCustomPointsAmount()) {
            return 'MageWorx_RewardPoints/payment/rewardpoints_custom_amount';
        }

        return 'MageWorx_RewardPoints/payment/rewardpoints';
    }
}
