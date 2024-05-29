<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\RewardPoints\Model\Total\Invoice;

class SpendPoints extends \Magento\Sales\Model\Order\Invoice\Total\AbstractTotal
{
    /**
     * @param \Magento\Sales\Model\Order\Invoice $invoice
     * @return $this
     */
    public function collect(\Magento\Sales\Model\Order\Invoice $invoice)
    {
        /** @var \Magento\Sales\Model\Order $order */
        $order = $invoice->getOrder();

        $baseRewardCurrencyAmount = $order->getBaseMwRwrdpointsCurAmnt();

        if ($baseRewardCurrencyAmount) {

            $rewardCurrencyAmountLeft     = $order->getMwRwrdpointsCurAmnt() - $order->getMwRwrdpointsCurAmntInvoice();
            $baseRewardCurrencyAmountLeft = $baseRewardCurrencyAmount - $order->getBaseMwRwrdpointsCurAmntInvoice();

            if ($baseRewardCurrencyAmountLeft > 0) {
                if ($baseRewardCurrencyAmountLeft < $invoice->getBaseGrandTotal()) {
                    $invoice->setGrandTotal($invoice->getGrandTotal() - $rewardCurrencyAmountLeft);
                    $invoice->setBaseGrandTotal($invoice->getBaseGrandTotal() - $baseRewardCurrencyAmountLeft);
                } else {
                    $rewardCurrencyAmountLeft     = $invoice->getGrandTotal();
                    $baseRewardCurrencyAmountLeft = $invoice->getBaseGrandTotal();

                    $invoice->setGrandTotal(0);
                    $invoice->setBaseGrandTotal(0);
                }

                $pointValue          = $order->getMwRwrdpointsAmnt() / $baseRewardCurrencyAmount;
                $rewardPointsBalance = $baseRewardCurrencyAmountLeft * $pointValue;

                $invoice->setMwRwrdpointsAmnt($rewardPointsBalance);
                $invoice->setMwRwrdpointsCurAmnt($rewardCurrencyAmountLeft);
                $invoice->setBaseMwRwrdpointsCurAmnt($baseRewardCurrencyAmountLeft);
            }
        }

        return $this;
    }
}
