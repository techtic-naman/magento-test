<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\RewardPoints\Model\Total\Creditmemo;

class SpendPoints extends \Magento\Sales\Model\Order\Creditmemo\Total\AbstractTotal
{
    /**
     * @param \Magento\Sales\Model\Order\Creditmemo $creditmemo
     * @return $this|void
     */
    public function collect(\Magento\Sales\Model\Order\Creditmemo $creditmemo)
    {
        $order = $creditmemo->getOrder();

        $baseRewardCurrencyAmount = $order->getBaseMwRwrdpointsCurAmnt();

        if ($baseRewardCurrencyAmount) {

            $rewardCurrencyAmountLeft     = $order->getMwRwrdpointsCurAmntInvoice(
                ) - $order->getMwRwrdpointsCurAmntRefund();
            $baseRewardCurrencyAmountLeft = $order->getBaseMwRwrdpointsCurAmntInvoice(
                ) - $order->getBaseMwRwrdpointsCurAmntRefund();

            if ($rewardCurrencyAmountLeft > 0) {
                if ($baseRewardCurrencyAmountLeft < $creditmemo->getBaseGrandTotal()) {
                    $creditmemo->setGrandTotal($creditmemo->getGrandTotal() - $rewardCurrencyAmountLeft);
                    $creditmemo->setBaseGrandTotal($creditmemo->getBaseGrandTotal() - $baseRewardCurrencyAmountLeft);
                } else {
                    $rewardCurrencyAmountLeft     = $creditmemo->getGrandTotal();
                    $baseRewardCurrencyAmountLeft = $creditmemo->getBaseGrandTotal();
                    $creditmemo->setGrandTotal(0);
                    $creditmemo->setBaseGrandTotal(0);
                    $creditmemo->setAllowZeroGrandTotal(true);
                }

                $rate = $order->getMwRwrdpointsAmnt() / $baseRewardCurrencyAmount;

                $rewardPointsBalance     = $baseRewardCurrencyAmountLeft * $rate;
                $rewardPointsBalanceLeft = $order->getMwRwrdpointsAmnt() - $order->getMwRwrdpointsAmntRefunded();

                if ($rewardPointsBalance > $rewardPointsBalanceLeft) {
                    $rewardPointsBalance = $rewardPointsBalanceLeft;
                }

                $creditmemo->setMwRwrdpointsAmnt($rewardPointsBalance);
                $creditmemo->setMwRwrdpointsCurAmnt($rewardCurrencyAmountLeft);
                $creditmemo->setBaseMwRwrdpointsCurAmnt($baseRewardCurrencyAmountLeft);
            }
        }

        return $this;
    }
}
