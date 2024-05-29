<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\RewardPoints\Observer\Display;

class AddBlockToCustomerAccountObserver implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var \MageWorx\RewardPoints\Helper\Data
     */
    protected $helperData;

    /**
     * AddBlockToCustomerAccountObserver constructor.
     *
     * @param \MageWorx\RewardPoints\Helper\Data $helperData
     */
    public function __construct(\MageWorx\RewardPoints\Helper\Data $helperData)
    {
        $this->helperData = $helperData;
    }

    /**
     * Add child block to product options tab
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if (!$this->helperData->isEnableForCustomer()) {
            return;
        }

        /** @var \Magento\Framework\View\LayoutInterface $layout */
        $layout = $observer->getLayout();

        /** @var \Magento\Framework\View\Element\AbstractBlock $block */
        $block = $layout->getBlock('customer_account_navigation');

        if ($block) {
            $block->addChild(
                'mageworx-rewardpoints-link',
                \Magento\Customer\Block\Account\SortLinkInterface::class,
                [
                    'path'      => 'rewardpoints/customer',
                    'label'     => __('My Reward Points'),
                    'sortOrder' => 45
                ]
            );
        }
    }
}
