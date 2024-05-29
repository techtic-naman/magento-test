<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\RewardPoints\Model\EventStrategy;

class SpendByOrderStrategy extends \MageWorx\RewardPoints\Model\AbstractEventStrategy
{
    /**
     * {@inheritdoc}
     */
    public function getPoints()
    {
        $order = $this->getEntity();

        return (-1) * $order->getMwRwrdpointsAmnt();
    }

    /**
     * {@inheritdoc}
     */
    public function getMessage(array $eventData, $comment = null)
    {
        $message = '';

        if (!empty($eventData['increment_id'])) {
            $message = __('Points were used for the order %1', $eventData['increment_id']);
        }

        return $message;
    }

    /**
     * {@inheritdoc}
     */
    public function getEventData()
    {
        if ($this->getEntity() && $this->getEntity()->getIncrementId()) {
            return ['increment_id' => $this->getEntity()->getIncrementId()];
        }

        return [];
    }

    /**
     * @return int|null
     */
    public function getExpirationPeriod()
    {
        return null;
    }

    /**
     * @return bool
     */
    public function useImmediateSending()
    {
        return false;
    }
}