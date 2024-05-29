<?php
/**
 *
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\RewardPoints\Observer;

use Magento\Framework\Event\ObserverInterface;

class AddPointsTriggerToOrderAdminObserver implements ObserverInterface
{
    /**
     * @var \MageWorx\RewardPoints\Helper\Data
     */
    protected $helperData;

    /**
     * @var \MageWorx\RewardPoints\Model\QuoteTriggerSetter
     */
    protected $quoteTriggerSetter;

    /**
     * AddPointsToOrderAdminObserver constructor.
     *
     * @param \MageWorx\RewardPoints\Helper\Customer $helperCustomer
     * @param \MageWorx\RewardPoints\Model\QuoteTriggerSetter $quoteTriggerSetter
     */
    public function __construct(
        \MageWorx\RewardPoints\Helper\Data $helperData,
        \MageWorx\RewardPoints\Model\QuoteTriggerSetter $quoteTriggerSetter
    ) {
        $this->helperData         = $helperData;
        $this->quoteTriggerSetter = $quoteTriggerSetter;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $request           = $observer->getEvent()->getRequest();
        $useMwRewardPoints = $request['payment']['use_reward_points'] ?? null;

        if ($useMwRewardPoints !== null) {
            /* @var $quote \Magento\Quote\Model\Quote */
            $quote = $observer->getEvent()->getOrderCreateModel()->getQuote();

            /** @var \Magento\Customer\Model\Customer $customer */
            $customer = $quote->getCustomer();

            if ($this->helperData->isEnableForCustomer($customer, $quote->getStoreId())) {
                $this->quoteTriggerSetter->setQuoteData(
                    $quote,
                    filter_var($useMwRewardPoints, FILTER_VALIDATE_BOOLEAN)
                );
            }
        }

        return $this;
    }
}
