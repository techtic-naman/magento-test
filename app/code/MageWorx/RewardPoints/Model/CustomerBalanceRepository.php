<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\RewardPoints\Model;

use MageWorx\RewardPoints\Api\CustomerBalanceRepositoryInterface;
use Magento\Framework\Exception\CouldNotSaveException;

/**
 * Class CustomerBalanceRepository
 */
class CustomerBalanceRepository implements CustomerBalanceRepositoryInterface
{
    /**
     * @var \MageWorx\RewardPoints\Model\ResourceModel\CustomerBalance
     */
    protected $resource;

    /**
     * @var \MageWorx\RewardPoints\Model\CustomerBalanceFactory
     */
    protected $customerBalanceFactory;

    /**
     * CustomerBalanceRepository constructor.
     *
     * @param ResourceModel\CustomerBalance $resource
     * @param CustomerBalanceRepository $customerBalanceFactory
     */
    public function __construct(
        \MageWorx\RewardPoints\Model\ResourceModel\CustomerBalance $resource,
        \MageWorx\RewardPoints\Model\CustomerBalanceFactory $customerBalanceFactory
    ) {
        $this->resource               = $resource;
        $this->customerBalanceFactory = $customerBalanceFactory;
    }

    /**
     * Save Customer point balance
     *
     * @param \MageWorx\RewardPoints\Api\Data\CustomerBalanceInterface $customerBalance
     * @return \MageWorx\RewardPoints\Api\Data\CustomerBalanceInterface
     * @throws CouldNotSaveException
     */
    public function save(\MageWorx\RewardPoints\Api\Data\CustomerBalanceInterface $customerBalance)
    {
        try {
            $this->resource->save($customerBalance);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(
                __('Could not save the customer point balance: %1', $exception->getMessage()),
                $exception
            );
        }

        return $customerBalance;
    }

    /**
     * Load Customer point balance by ID
     *
     * @param int $customerBalanceId
     * @return \MageWorx\RewardPoints\Api\Data\CustomerBalanceInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById($customerBalanceId)
    {
        $customerBalance = $this->customerBalanceFactory->create();
        $this->resource->load($customerBalance, $customerBalanceId);
        if (!$customerBalance->getId()) {
            throw new \Magento\Framework\Exception\NoSuchEntityException(
                __("The customer point balance wasn't saved: %1.", $customerBalanceId)
            );
        }

        return $customerBalance;
    }

    /**
     * Load Customer point balance by Customer Data
     *
     * @param int $customerId
     * @param int $websiteId
     * @return \MageWorx\RewardPoints\Api\Data\CustomerBalanceInterface|CustomerBalance
     */
    public function getByCustomer($customerId, $websiteId)
    {
        $customerBalance = $this->customerBalanceFactory->create();
        $this->resource->loadByCustomer($customerBalance, $customerId, $websiteId);
        if (!$customerBalance->getId()) {
            $customerBalance->setCustomerId($customerId)->setWebsiteId($websiteId);
        }

        return $customerBalance;
    }
}
