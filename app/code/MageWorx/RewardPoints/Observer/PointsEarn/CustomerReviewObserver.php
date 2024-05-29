<?php
/**
 *
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\RewardPoints\Observer\PointsEarn;

use Magento\Framework\Event\ObserverInterface;


class CustomerReviewObserver implements ObserverInterface
{
    /**
     * @var \MageWorx\RewardPoints\Helper\Data
     */
    protected $helperData;

    /**
     * @var \MageWorx\RewardPoints\Model\PointTransactionApplier
     */
    protected $pointTransactionApplier;

    /**
     * @var CustomerRepositoryInterface
     */
    protected $customerRepository;

    /**
     * @var string
     */
    protected $eventCode = \MageWorx\RewardPoints\Model\Rule::CUSTOMER_REVIEW_EVENT;

    /**
     * CustomerReviewObserver constructor.
     *
     * @param \MageWorx\RewardPoints\Helper\Data $helperData
     * @param \MageWorx\RewardPoints\Model\PointTransactionApplier $pointTransactionApplier
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
     */
    public function __construct(
        \MageWorx\RewardPoints\Helper\Data $helperData,
        \MageWorx\RewardPoints\Model\PointTransactionApplier $pointTransactionApplier,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
    ) {
        $this->helperData              = $helperData;
        $this->pointTransactionApplier = $pointTransactionApplier;
        $this->customerRepository      = $customerRepository;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this|void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /* @var $review \Magento\Review\Model\Review */
        $review = $observer->getEvent()->getObject();

        if (!$review) {
            return $this;
        }

        if (!$this->helperData->isEnable()) {
            return $this;
        }

        if (!$review->isApproved() || !$review->getCustomerId()) {
            return $this;
        }

        $customer = $this->customerRepository->getById($review->getCustomerId());

        if (!$this->helperData->isEnableForCustomer($customer)) {
            return $this;
        }

        $this->pointTransactionApplier->applyTransaction(
            $this->eventCode,
            $customer,
            $review->getStoreId(),
            $review
        );

        return $this;
    }
}
