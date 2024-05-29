<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\RewardPoints\Model;

class PointTransactionMessageMaker
{
    /**
     * @var \MageWorx\RewardPoints\Model\EventStrategyFactory
     */
    protected $eventStrategyFactory;

    /**
     * PointTransactoinMessageMaker constructor.
     *
     * @param EventStrategyFactory $eventStrategyFactory
     */
    public function __construct(
        \MageWorx\RewardPoints\Model\EventStrategyFactory $eventStrategyFactory
    ) {
        $this->eventStrategyFactory = $eventStrategyFactory;
    }

    /**
     * @param string $eventCode
     * @param array $eventData
     * @param string $comment
     * @return string
     */
    public function getTransactionMessage($eventCode, array $eventData, $comment = '')
    {
        $message = '';

        /**@todo add to local registry */
        $eventStrategy = $this->eventStrategyFactory->create($eventCode);

        if ($eventStrategy) {
            $message = $eventStrategy->getMessage($eventData, $comment);
        }

        return $message;
    }
}