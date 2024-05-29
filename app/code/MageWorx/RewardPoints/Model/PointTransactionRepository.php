<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\RewardPoints\Model;

use MageWorx\RewardPoints\Api\PointTransactionRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotSaveException;

class PointTransactionRepository implements PointTransactionRepositoryInterface
{
    /**
     * @var \MageWorx\RewardPoints\Model\ResourceModel\PointTransaction
     */
    protected $resource;

    /**
     * @var \MageWorx\RewardPoints\Model\PointTransactionFactory
     */
    protected $pointTransactionFactory;

    /**
     * PointTransactionRepository constructor.
     *
     * @param ResourceModel\PointTransaction $resource
     * @param PointTransactionFactory $pointTransactionFactory
     */
    public function __construct(
        \MageWorx\RewardPoints\Model\ResourceModel\PointTransaction $resource,
        \MageWorx\RewardPoints\Model\PointTransactionFactory $pointTransactionFactory
    ) {
        $this->resource                = $resource;
        $this->pointTransactionFactory = $pointTransactionFactory;
    }

    /**
     * @param \MageWorx\RewardPoints\Api\Data\PointTransactionInterface $pointTransaction
     * @return \MageWorx\RewardPoints\Api\Data\PointTransactionInterface|mixed
     */
    public function save(\MageWorx\RewardPoints\Api\Data\PointTransactionInterface $pointTransaction)
    {
        try {
            $this->resource->save($pointTransaction);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(
                __('Could not save the reward points transaction: %1', $exception->getMessage()),
                $exception
            );
        }

        return $pointTransaction;
    }

    /**
     * Load customer reward points transaction by ID
     *
     * @param int $pointTransactionId
     * @return \MageWorx\RewardPoints\Api\Data\PointTransactionInterface|PointTransaction
     */
    public function getById($pointTransactionId)
    {
        $pointTransaction = $this->pointTransactionFactory->create();
        $this->resource->load($pointTransactionId);
        if (!$pointTransaction->getId()) {
            throw new NoSuchEntityException(
                __('Reward point transaction with ID "%1" does not exist.', $pointTransactionId)
            );
        }

        return $pointTransaction;
    }
}
