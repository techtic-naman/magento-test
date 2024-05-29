<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\RewardPoints\Model\EventStrategy;

class RefundStrategy extends \MageWorx\RewardPoints\Model\AbstractEventStrategy
{
    /**
     * @var \MageWorx\RewardPoints\Helper\Price
     */
    protected $helperPrice;

    /**
     * RefundStrategy constructor.
     *
     * @param \MageWorx\RewardPoints\Helper\Data $helperData
     * @param \MageWorx\RewardPoints\Helper\Price $helperPrice
     * @param array $data
     */
    public function __construct(
        \MageWorx\RewardPoints\Helper\Data $helperData,
        \MageWorx\RewardPoints\Helper\Price $helperPrice,
        array $data = []
    ) {
        $this->helperPrice = $helperPrice;
        parent::__construct($helperData, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function getMessage(array $eventData, $comment = null)
    {
        $message = '';

        if (!empty($eventData['increment_id'])) {
            $message = __('Points were added for the refunded order %1', $eventData['increment_id']);
        }

        return $message;
    }

    /**
     * {@inheritdoc}
     */
    public function getPoints()
    {
        return $this->getEntity()->getMwRwrdpointsAmntRefund();
    }

    /**
     * {@inheritdoc}
     */
    public function getEventData()
    {
        $eventData['increment_id'] = $this->getEntity()->getOrder()->getIncrementId();

        return $eventData;
    }

    /**
     * {@inheritdoc}
     */
    public function processAfterTransactionSave($pointTransaction)
    {
        if ($pointTransaction->getId() && $pointTransaction->getPointsDelta()) {

            /** @var \Magento\Sales\Model\Order $order */
            $order = $this->getEntity()->getOrder();

            $order->addStatusHistoryComment(
                __(
                    'We refunded %1 which were used for this order.',
                    $this->helperPrice->getFormattedPoints($pointTransaction->getPointsDelta())
                )
            );

            $order->save();
        }

        return parent::processAfterTransactionSave($pointTransaction);
    }
}