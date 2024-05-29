<?php
/**
 *
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\RewardPoints\Observer\PointsEarn;

use Magento\Framework\Event\ObserverInterface;


class NewsletterSubscriptionObserver implements ObserverInterface
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
    protected $eventCode = \MageWorx\RewardPoints\Model\Rule::CUSTOMER_NEWSLETTER_SUBSCRIPTION_EVENT;

    /**
     * NewsletterSubscriptionObserver constructor.
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
     * @return $this|void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /* @var $subscriber \Magento\Newsletter\Model\Subscriber */
        $subscriber = $observer->getEvent()->getSubscriber();

        if (!$subscriber->getCustomerId()) {
            return $this;
        }

        if (!$subscriber->isObjectNew()) {
            return $this;
        }

        $storeId = $subscriber->getStoreId();

        if (!$this->helperData->isEnable($storeId)) {
            return $this;
        }

        $customer = $this->customerRepository->getById($subscriber->getCustomerId());

        if (!$this->helperData->isEnableForCustomer($customer, $storeId)) {
            return $this;
        }

        $this->pointTransactionApplier->applyTransaction(
            $this->eventCode,
            $customer,
            $storeId,
            $subscriber
        );

        return $this;
    }
}
