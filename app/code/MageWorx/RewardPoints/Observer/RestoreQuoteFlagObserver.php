<?php
/**
 *
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\RewardPoints\Observer;

use Magento\Framework\Event\ObserverInterface;

class RestoreQuoteFlagObserver implements ObserverInterface
{
    /**
     * Move reward points flag to new quote
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $flag = $observer->getEvent()->getSource()->getUseMwRewardPoints();
        $observer->getEvent()->getQuote()->setUseMwRewardPoints($flag);

        $amount = $observer->getEvent()->getSource()->getMwRequestedPoints();
        $observer->getEvent()->getQuote()->setMwRequestedPoints($amount);

        return $this;
    }
}
