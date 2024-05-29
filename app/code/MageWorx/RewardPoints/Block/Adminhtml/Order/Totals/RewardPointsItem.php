<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\RewardPoints\Block\Adminhtml\Order\Totals;


class RewardPointsItem extends \Magento\Sales\Block\Adminhtml\Order\Totals\Item
{
    /**
     * @var \MageWorx\RewardPoints\Helper\Price
     */
    protected $helperPrice;

    /**
     * RewardPointsItem constructor.
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Sales\Helper\Admin $adminHelper
     * @param \MageWorx\RewardPoints\Helper\Price $helperPrice
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Sales\Helper\Admin $adminHelper,
        \MageWorx\RewardPoints\Helper\Price $helperPrice,
        array $data = []
    ) {
        $this->helperPrice = $helperPrice;
        parent::__construct($context, $registry, $adminHelper, $data);
    }

    /**
     * @return \Magento\Framework\Phrase
     */
    public function getFormattedRewardPointsAmount()
    {
        return $this->helperPrice->getFormattedPoints($this->getSource()->getMwRwrdpointsAmnt());
    }
}