<?php
/**
 *
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\RewardPoints\Observer;

use Magento\Framework\Event\ObserverInterface;

class AddInvoicedPointCurrencyToOrderObserver implements ObserverInterface
{
    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /* @var $invoice \Magento\Sales\Model\Order\Invoice */
        $invoice = $observer->getEvent()->getInvoice();

        if ($invoice->getMwRwrdpointsCurAmnt()) {
            $order = $invoice->getOrder();
            $order->setMwRwrdpointsCurAmntInvoice(
                $order->getMwRwrdpointsCurAmntInvoice() + $invoice->getMwRwrdpointsCurAmnt()
            );
            $order->setBaseMwRwrdpointsCurAmntInvoice(
                $order->getBaseMwRwrdpointsCurAmntInvoice() + $invoice->getBaseMwRwrdpointsCurAmnt()
            );
        }

        return $this;
    }
}
