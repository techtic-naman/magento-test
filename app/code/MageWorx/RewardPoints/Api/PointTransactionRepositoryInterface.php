<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\RewardPoints\Api;

/**
 * Customer Point Transaction CRUD interface
 */
interface PointTransactionRepositoryInterface
{
    /**
     * Save point transaction.
     *
     * @param \MageWorx\RewardPoints\Api\Data\PointTransactionInterface $pointTransaction
     * @return \MageWorx\RewardPoints\Api\Data\PointTransactionInterface $pointTransaction
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(\MageWorx\RewardPoints\Api\Data\PointTransactionInterface $pointTransaction);

    /**
     * Retrieve point transaction.
     *
     * @param int $pointTransactionId
     * @return \MageWorx\RewardPoints\Api\Data\PointTransactionInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($pointTransactionId);
}
