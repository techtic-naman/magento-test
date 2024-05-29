<?php
/**
 *
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\RewardPoints\Observer;

use Magento\Framework\Event\ObserverInterface;

class AddRewardPointsToRefundObserver implements ObserverInterface
{
    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /** @var \Magento\Sales\Model\Order\Creditmemo $creditmemo */
        $creditmemo = $observer->getEvent()->getCreditmemo();
        $input      = $observer->getEvent()->getInput();

        if (!empty($input['mageworx_rewardpoints_refund_points'])) {
            $pointAmount = (double)$input['mageworx_rewardpoints_refund_points'];

            if ($creditmemo->getMwRwrdpointsAmnt() > $pointAmount) {
                $creditmemo->setMwRwrdpointsAmntRefund($pointAmount);
            } else {
                $creditmemo->setMwRwrdpointsAmntRefund($creditmemo->getMwRwrdpointsAmnt());
            }
        }

        return $this;
    }
}
