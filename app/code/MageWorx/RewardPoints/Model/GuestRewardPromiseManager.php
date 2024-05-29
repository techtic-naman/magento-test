<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\RewardPoints\Model;

use \MageWorx\RewardPoints\Api\RewardPromiseManagerInterface;

class GuestRewardPromiseManager implements \MageWorx\RewardPoints\Api\GuestRewardPromiseManagerInterface
{
    /**
     * @var RewardPromiseManagerInterface
     */
    protected $rewardPromiseManager;

    /**
     * GuestRewardPromiseManager constructor.
     *
     * @param RewardPromiseManagerInterface $rewardPromiseManager
     */
    public function __construct(
        \MageWorx\RewardPoints\Api\RewardPromiseManagerInterface $rewardPromiseManager
    ) {
        $this->rewardPromiseManager = $rewardPromiseManager;
    }

    /**
     * @param int[] $productIds
     * @return array|\MageWorx\RewardPoints\Api\Data\RewardPromiseInterface[]
     */
    public function getByProductIds(array $productIds): array
    {
        return $this->rewardPromiseManager->findByProductIds($productIds);
    }
}
