<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\RewardPoints\Observer\SendMessage;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;

class OrderSuccessfullyPlacedObserver implements ObserverInterface
{
    /**
     * @var \MageWorx\RewardPoints\Model\PointTransactionEmailHolder
     */
    protected $pointTransactionEmailHolder;

    /**
     * @var string
     */
    protected $eventCode = \MageWorx\RewardPoints\Model\EventStrategyFactory::ORDER_PLACED_SPEND_EVENT;

    /**
     * OrderSuccessfullyPlacedObserver constructor.
     *
     * @param \MageWorx\RewardPoints\Model\PointTransactionEmailHolder $pointTransactionEmailHolder
     */
    public function __construct(
        \MageWorx\RewardPoints\Model\PointTransactionEmailHolder $pointTransactionEmailHolder
    ) {
        $this->pointTransactionEmailHolder = $pointTransactionEmailHolder;
    }

    /**
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        $this->pointTransactionEmailHolder->sendEmail(
            \MageWorx\RewardPoints\Model\EventStrategyFactory::ORDER_PLACED_SPEND_EVENT
        );

        $this->pointTransactionEmailHolder->sendEmail(
            \MageWorx\RewardPoints\Model\Rule::ORDER_PLACED_EARN_EVENT
        );
    }
}
