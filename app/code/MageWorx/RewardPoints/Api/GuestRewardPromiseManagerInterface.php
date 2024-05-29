<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace MageWorx\RewardPoints\Api;

interface GuestRewardPromiseManagerInterface
{
    /**
     * @param int[] $productIds
     * @return \MageWorx\RewardPoints\Api\Data\RewardPromiseInterface[]
     */
    public function getByProductIds(array $productIds): array;
}
