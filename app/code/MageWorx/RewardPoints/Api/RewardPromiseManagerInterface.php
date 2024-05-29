<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace MageWorx\RewardPoints\Api;


interface RewardPromiseManagerInterface
{
    /**
     * @param int $customerId
     * @param int[] $productIds
     * @return \MageWorx\RewardPoints\Api\Data\RewardPromiseInterface[]
     */
    public function getByProductIds(int $customerId, array $productIds): array;
}
