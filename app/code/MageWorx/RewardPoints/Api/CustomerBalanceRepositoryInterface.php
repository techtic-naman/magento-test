<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\RewardPoints\Api;

/**
 * Customer Point Balance CRUD interface
 */
interface CustomerBalanceRepositoryInterface
{
    /**
     * Save customer point balance.
     *
     * @param \MageWorx\RewardPoints\Api\Data\CustomerBalanceInterface $customerBalance
     * @return \MageWorx\RewardPoints\Api\Data\CustomerBalanceInterface
     * @throws \Magento\Framework\Exception\InputException If there is a problem with the input
     * @throws \Magento\Framework\Exception\NoSuchEntityException If a rule ID is sent but the rule does not exist
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(\MageWorx\RewardPoints\Api\Data\CustomerBalanceInterface $customerBalance);

    /**
     * Get customer point balance by ID.
     *
     * @param int $customerBalanceId
     * @return \MageWorx\RewardPoints\Api\Data\CustomerBalanceInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($customerBalanceId);

    /**
     * Get customer point balance by customer ID and website ID.
     *
     * @param int $customerId
     * @param int $websiteId
     * @return \MageWorx\RewardPoints\Api\Data\CustomerBalanceInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getByCustomer($customerId, $websiteId);

}
