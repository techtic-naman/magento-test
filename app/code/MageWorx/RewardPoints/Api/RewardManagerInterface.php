<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\RewardPoints\Api;

interface RewardManagerInterface
{
    /**
     * Returns information for a reward points in a specified cart.
     *
     * @param int $cartId The cart ID.
     * @return string The reward points data.
     * @throws \Magento\Framework\Exception\NoSuchEntityException The specified cart does not exist.
     */
    public function get($cartId);

    /**
     * Adds customer's reward points to a specified cart.
     *
     * @param int $cartId The cart ID.
     * @param float|null $pointsAmount
     * @return bool
     * @throws \Magento\Framework\Exception\CouldNotSaveException The specified reward points not be added.
     */
    public function set($cartId, $pointsAmount = null);

    /**
     * Adds all customer's reward points to a specified cart.
     *
     * @param int $cartId The cart ID.
     * @return bool
     * @throws \Magento\Framework\Exception\CouldNotSaveException The specified reward points not be added.
     */
    public function setAll($cartId);

    /**
     * Deletes all reward points from a specified cart.
     *
     * @param int $cartId The cart ID.
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException The specified cart does not exist.
     * @throws \Magento\Framework\Exception\CouldNotDeleteException The specified coupon could not be deleted.
     */
    public function remove($cartId);

    /**
     * Save customer's reward points balance.
     *
     * @param \MageWorx\RewardPoints\Api\Data\CustomerBalanceInterface $customerBalance
     * @return \MageWorx\RewardPoints\Api\Data\CustomerBalanceInterface
     * @throws \Magento\Framework\Exception\LocalizedException|false
     */
    public function saveBalance(\MageWorx\RewardPoints\Api\Data\CustomerBalanceInterface $customerBalance);

    /**
     * Apply transaction.
     * Required fields: customer_id, store_id, points_delta
     *
     * @param Data\PointTransactionInterface $pointTransaction
     * @return \MageWorx\RewardPoints\Api\Data\CustomerBalanceInterface
     * @throws \Magento\Framework\Exception\LocalizedException|false
     * @since 1.5.0
     */
    public function applyTransaction(\MageWorx\RewardPoints\Api\Data\PointTransactionInterface $pointTransaction);
}
