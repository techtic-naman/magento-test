<?php
/**
 *
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\RewardPoints\Observer;

use Magento\Framework\Event\ObserverInterface;

class AddPaymentDiscountObserver implements ObserverInterface
{
    /**
     * Set rewardpoints amount as payment discount
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /** @var \Magento\Payment\Model\Cart $cart */
        $cart     = $observer->getCart();
        $discount = abs((float)$cart->getSalesModel()->getDataUsingMethod('base_mw_rwrdpoints_cur_amnt'));

        if ($discount > 0) {
            $cart->addDiscount($discount);
        }

        return $this;
    }
}
