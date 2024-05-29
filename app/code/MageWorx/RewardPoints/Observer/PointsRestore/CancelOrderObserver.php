<?php
/**
 *
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\RewardPoints\Observer\PointsRestore;

use Magento\Framework\Event\ObserverInterface;


class CancelOrderObserver implements ObserverInterface
{
    /**
     * @var \MageWorx\RewardPoints\Helper\Data
     */
    protected $helperData;

    /**
     * @var CustomerRepositoryInterface
     */
    protected $customerRepository;

    /**
     * @var \MageWorx\RewardPoints\Model\PointTransactionApplier
     */
    protected $pointTransactionApplier;

    /**
     * @var string
     */
    protected $eventCode = \MageWorx\RewardPoints\Model\EventStrategyFactory::ORDER_CANCEL_RESTORE_EVENT;

    /**
     * CancelOrderObserver constructor.
     *
     * @param \MageWorx\RewardPoints\Helper\Data $helperData
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
     * @param \MageWorx\RewardPoints\Model\PointTransactionApplier $pointTransactionApplier
     */
    public function __construct(
        \MageWorx\RewardPoints\Helper\Data $helperData,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \MageWorx\RewardPoints\Model\PointTransactionApplier $pointTransactionApplier
    ) {
        $this->helperData              = $helperData;
        $this->pointTransactionApplier = $pointTransactionApplier;
        $this->customerRepository      = $customerRepository;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /** @var \Magento\Sales\Model\Order $order */
        $order = $observer->getEvent()->getOrder();

        if (!$this->helperData->isEnable($order->getStoreId())) {
            return $this;
        }

        if (!$this->helperData->getIsReturnSpentPointOnCancellation($order->getStoreId())) {
            return $this;
        }

        if ($order->getMwRwrdpointsAmnt() <= 0) {
            return $this;
        }

        try {
            $customer = $this->customerRepository->getById($order->getCustomerId());
        } catch (\Magento\Framework\Exception\NoSuchEntityException $exception) {
            //Customer was deleted
            return $this;
        }

        if (!$this->helperData->isEnableForCustomer($customer, $order->getStoreId())) {
            return $this;
        }

        $this->pointTransactionApplier->applyTransaction(
            $this->eventCode,
            $customer,
            $order->getStoreId(),
            $order
        );

        return $this;
    }
}
