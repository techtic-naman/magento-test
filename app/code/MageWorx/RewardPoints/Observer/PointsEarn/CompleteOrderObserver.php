<?php
/**
 *
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\RewardPoints\Observer\PointsEarn;

use Magento\Framework\Event\ObserverInterface;

class CompleteOrderObserver implements ObserverInterface
{
    /**
     * @var \MageWorx\RewardPoints\Model\PointTransactionApplier
     */
    protected $pointTransactionApplier;

    /**
     * @var \MageWorx\RewardPoints\Helper\Data
     */
    protected $helperData;

    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    protected $customerRepository;

    /**
     * @var \Magento\Framework\Serialize\Serializer\Json
     */
    protected $serializer;

    /**
     * @var string
     */
    protected $eventCode = \MageWorx\RewardPoints\Model\Rule::ORDER_PLACED_EARN_EVENT;

    /**
     * CompleteOrderObserver constructor.
     *
     * @param \MageWorx\RewardPoints\Helper\Data $helperData
     * @param \MageWorx\RewardPoints\Model\PointTransactionApplier $pointTransactionApplier
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
     * @param \Magento\Framework\Serialize\Serializer\Json $serializer
     */
    public function __construct(
        \MageWorx\RewardPoints\Helper\Data $helperData,
        \MageWorx\RewardPoints\Model\PointTransactionApplier $pointTransactionApplier,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Magento\Framework\Serialize\Serializer\Json $serializer
    ) {
        $this->helperData              = $helperData;
        $this->pointTransactionApplier = $pointTransactionApplier;
        $this->customerRepository      = $customerRepository;
        $this->serializer              = $serializer;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /* @var $order \Magento\Sales\Model\Order */
        $order = $observer->getEvent()->getOrder();

        if (!$order instanceof \Magento\Sales\Api\Data\OrderInterface) {
            return $this;
        }

        if ($order->getCustomerIsGuest()) {
            return $this;
        }

        if (!$order->getCustomerId()) {
            return $this;
        }

        if ($order->getBaseTotalPaid() <= 0) {
            return $this;
        }

        if (!$this->isOrderPaidCompletelyNow($order)) {
            return $this;
        }

        $earnPointsSerializedData = $order->getMwEarnPointsData();

        if ($earnPointsSerializedData === null) {
            return $this;
        }

        $earnPointsData = $this->serializer->unserialize($earnPointsSerializedData);

        $customer = $this->customerRepository->getById($order->getCustomerId());
        $storeId  = $order->getStoreId();

        if (is_array($earnPointsData) && count($earnPointsData) > 0) {
            $this->pointTransactionApplier->applyTransaction(
                $this->eventCode,
                $customer,
                $storeId,
                $order,
                $earnPointsData,
                false
            );
        }

        return $this;
    }

    /**
     * @param \Magento\Sales\Model\Order $order
     * @return bool
     */
    protected function isOrderPaidCompletelyNow($order)
    {
        $leftPaid              = $order->getBaseGrandTotal() - $order->getBaseTotalPaid(
            ) - $order->getBaseSubtotalCanceled();
        $isOrderPaidCompletely = (double)$order->getBaseTotalPaid() > 0 && $leftPaid < 0.0001;

        if (!$order->getOrigData('base_grand_total')) {
            return $isOrderPaidCompletely;
        }

        $previouslyLeftPaid = $order->getOrigData('base_grand_total')
            - $order->getOrigData('base_total_paid')
            - $order->getOrigData('base_subtotal_canceled');

        return $isOrderPaidCompletely && $previouslyLeftPaid >= 0.0001;
    }
}
