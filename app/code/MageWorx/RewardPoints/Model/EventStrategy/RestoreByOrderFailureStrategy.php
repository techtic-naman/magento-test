<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\RewardPoints\Model\EventStrategy;

class RestoreByOrderFailureStrategy extends \MageWorx\RewardPoints\Model\AbstractEventStrategy
{
    /**
     * @var \MageWorx\RewardPoints\Helper\Price
     */
    protected $helperPrice;

    /**
     * RestoreByOrderStrategy constructor.
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
        $message = __('Points were restored for an unsuccessful order');
        return $message;
    }

    /**
     * {@inheritdoc}
     */
    public function getIsPossibleSendNotification()
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function getPoints()
    {
        return $this->getEntity()->getMwRwrdpointsAmnt();
    }

    /**
     * {@inheritdoc}
     */
    public function getEventData()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function processAfterTransactionSave($pointTransaction)
    {
        return parent::processAfterTransactionSave($pointTransaction);
    }
}