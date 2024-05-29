<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\RewardPoints\Observer\PointsSpend;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use MageWorx\RewardPoints\Model\PointTransactionFactory;
use Magento\Framework\Exception\PaymentException;

class QuoteToOrderObserver implements ObserverInterface
{
    /**
     * @var \MageWorx\RewardPoints\Api\CustomerBalanceRepositoryInterface
     */
    protected $customerBalanceRepository;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $session;

    /**
     * @var PointTransactionFactory
     */
    protected $pointTransactionFactory;

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
    protected $eventCode = \MageWorx\RewardPoints\Model\EventStrategyFactory::ORDER_PLACED_SPEND_EVENT;

    /**
     * QuoteToOrderObserver constructor.
     *
     * @param \MageWorx\RewardPoints\Api\CustomerBalanceRepositoryInterface $customerBalanceRepository
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Checkout\Model\Session $session
     * @param \MageWorx\RewardPoints\Model\PointTransactionApplier $pointTransactionApplier
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
     */
    public function __construct(
        \MageWorx\RewardPoints\Api\CustomerBalanceRepositoryInterface $customerBalanceRepository,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Checkout\Model\Session $session,
        \MageWorx\RewardPoints\Model\PointTransactionApplier $pointTransactionApplier,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
    ) {
        $this->customerBalanceRepository = $customerBalanceRepository;
        $this->storeManager              = $storeManager;
        $this->session                   = $session;
        $this->pointTransactionApplier   = $pointTransactionApplier;
        $this->customerRepository        = $customerRepository;
    }

    /**
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        /** @var $quote \Magento\Quote\Model\Quote $quote */
        $quote = $observer->getEvent()->getQuote();

        if ($quote->getBaseMwRwrdpointsCurAmnt() <= 0) {
            return $this;
        }

        /* @var $order \Magento\Sales\Model\Order */
        $order = $observer->getEvent()->getOrder();

        if ($order->getMwRwrdpointsAmnt() > 0 && !$this->hasNeededPoints($order)) {

            $this->session->setUpdateSection(
                'payment-method'
            )->setGotoSection(
                'payment'
            );

            throw new PaymentException(__('There are not enough reward points for the purchase'));
        }

        /** @var  \Magento\Quote\Model\Quote\Address $address */
        $address = $observer->getEvent()->getAddress();

        $source = $quote->getIsMultiShipping() ? $address : $quote;

        $order->setMwRwrdpointsAmnt($source->getMwRwrdpointsAmnt());
        $order->setMwRwrdpointsCurAmnt($source->getMwRwrdpointsCurAmnt());
        $order->setBaseMwRwrdpointsCurAmnt($source->getBaseMwRwrdpointsCurAmnt());

        $customer = $this->customerRepository->getById($order->getCustomerId());

        $this->pointTransactionApplier->applyTransaction(
            $this->eventCode,
            $customer,
            $order->getStoreId(),
            $order
        );
    }

    /**
     * @param \Magento\Sales\Api\Data\OrderInterface $order
     * @return bool
     */
    protected function hasNeededPoints($order)
    {
        $neededPoints = $order->getMwRwrdpointsAmnt();

        $customerBalance = $this->customerBalanceRepository->getByCustomer(
            $order->getCustomerId(),
            $this->storeManager->getStore($order->getStoreId())->getWebsiteId()
        );

        return $neededPoints <= $customerBalance->getPointsAmount();
    }
}
