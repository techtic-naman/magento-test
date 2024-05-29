<?php
/**
 * Copyright © MageWorx
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace MageWorx\RewardPoints\Model\Klarna\OrderLines\RewardPoints;

class Validator
{

    /**
     * Returns true if its collectable
     *
     * @param \Klarna\Orderlines\Model\Container\DataHolder $dataHolder
     * @return bool
     */
    public function isCollectable($dataHolder): bool
    {
        return isset($dataHolder->getTotals()['mageworx_rewardpoints_spend']);
    }

    /**
     * Returns true if its fetchable
     *
     * @param \Klarna\Orderlines\Model\Container\Parameter $parameter
     * @return bool
     */
    public function isFetchable($parameter): bool
    {
        // Reward points total always negative
        return $parameter->getRewardTotalAmount() < 0;
    }
}
