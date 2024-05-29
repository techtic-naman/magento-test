<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\Rewardpoints\Model\Plugin;

use Magento\Quote\Model\Quote;

class ResetPointsDataBeforeCollectPlugin
{
    /**
     * Reset quote reward points data
     *
     * @param \Magento\Quote\Model\Quote\TotalsCollector $subject
     * @param \Magento\Quote\Model\Quote $quote
     *
     * @return void
     */
    public function beforeCollect(
        \Magento\Quote\Model\Quote\TotalsCollector $subject,
        Quote $quote
    ) {
        $quote->setMwRwrdpointsCurAmnt(0)
              ->setBaseMwRwrdpointsCurAmnt(0)
              ->setMwRwrdpointsAmnt(0);
    }
}
