<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\RewardPoints\Model;

use Magento\Framework\Exception\CouldNotSaveException;

class RewardManager implements \MageWorx\RewardPoints\Api\RewardManagerInterface
{
    /**
     * @var \MageWorx\RewardPoints\Helper\Data
     */
    protected $helperData;

    /**
     * @var \MageWorx\RewardPoints\Helper\Store
     */
    protected $helperStore;

    /**
     * @var \MageWorx\RewardPoints\Model\CustomerBalanceFactory
     */
    protected $customerBalanceFactory;

    /**
     * @var \Magento\Quote\Api\CartRepositoryInterface
     */
    protected $quoteRepository;

    /**
     * @var \MageWorx\RewardPoints\Model\QuoteTriggerSetter
     */
    protected $quoteTriggerSetter;

    /**
     * @var \MageWorx\RewardPoints\Model\CustomerBalanceRepository
     */
    protected $customerBalanceRepository;

    /**
     * @var \MageWorx\RewardPoints\Model\PointTransactionApplier
     */
    protected $pointTransactionApplier;

    /**
     * @var \Magento\Framework\DataObjectFactory
     */
    protected $dataObjectFactory;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository ,
     */
    protected $customerRepository;

    /**
     * @var \Magento\Customer\Model\Config\Share
     */
    private $shareConfig;

    /**
     * @var \MageWorx\RewardPoints\Helper\ExpirationDate
     */
    protected $expirationDateHelper;

    /**
     * RewardManager constructor.
     *
     * @param \MageWorx\RewardPoints\Helper\Data $helperData
     * @param \MageWorx\RewardPoints\Helper\Store $helperStore
     * @param \Magento\Quote\Api\CartRepositoryInterface $quoteRepository
     * @param QuoteTriggerSetter $quoteTriggerSetter
     * @param \MageWorx\RewardPoints\Api\CustomerBalanceRepositoryInterface $customerBalanceRepository
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param PointTransactionApplier $pointTransactionApplier
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
     * @param \Magento\Framework\DataObjectFactory $dataObjectFactory
     * @param \Magento\Customer\Model\Config\Share $shareConfig
     * @param \MageWorx\RewardPoints\Helper\ExpirationDate $expirationDateHelper
     */
    public function __construct(
        \MageWorx\RewardPoints\Helper\Data $helperData,
        \MageWorx\RewardPoints\Helper\Store $helperStore,
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository,
        \MageWorx\RewardPoints\Model\QuoteTriggerSetter $quoteTriggerSetter,
        \MageWorx\RewardPoints\Api\CustomerBalanceRepositoryInterface $customerBalanceRepository,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \MageWorx\RewardPoints\Model\PointTransactionApplier $pointTransactionApplier,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Magento\Framework\DataObjectFactory $dataObjectFactory,
        \Magento\Customer\Model\Config\Share $shareConfig,
        \MageWorx\RewardPoints\Helper\ExpirationDate $expirationDateHelper
    ) {
        $this->helperData                = $helperData;
        $this->helperStore               = $helperStore;
        $this->quoteRepository           = $quoteRepository;
        $this->quoteTriggerSetter        = $quoteTriggerSetter;
        $this->customerBalanceRepository = $customerBalanceRepository;
        $this->storeManager              = $storeManager;
        $this->pointTransactionApplier   = $pointTransactionApplier;
        $this->dataObjectFactory         = $dataObjectFactory;
        $this->customerRepository        = $customerRepository;
        $this->shareConfig               = $shareConfig;
        $this->expirationDateHelper      = $expirationDateHelper;
    }

    /**
     * {@inheritdoc}
     */
    public function remove($cartId)
    {
        try {
            /** @var  \Magento\Quote\Model\Quote $quote */
            $quote = $this->quoteRepository->getActive($cartId);

            if (!$quote->getUseMwRewardPoints()) {
                return false;
            }

            $result = $this->quoteTriggerSetter->setQuoteData($quote, false);

            if (!$result) {
                return false;
            }

            $this->quoteTriggerSetter->setQuoteData($quote, false);
            $quote->collectTotals();
            $this->quoteRepository->save($quote);
        } catch (\Exception $e) {
            throw new CouldNotSaveException(__('Could not remove reward points'));
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function set($cartId, $pointsAmount = null)
    {
        try {
            /* @var $quote \Magento\Quote\Model\Quote */
            $quote = $this->quoteRepository->getActive($cartId);

            if ($this->helperData->isEnableForCustomer($quote->getCustomer(), $quote->getStoreId())) {

                $result = $this->quoteTriggerSetter->setQuoteData($quote, true, $pointsAmount);

                if (!$result) {
                    return false;
                }

                $quote->collectTotals();
                $this->quoteRepository->save($quote);
            }
        } catch (\Exception $e) {
            throw new CouldNotSaveException(__('Could not apply reward points'));
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function setAll($cartId)
    {
        return $this->set($cartId, null);
    }

    /**
     * {@inheritdoc}
     */
    public function get($cartId)
    {
        /** @var  \Magento\Quote\Model\Quote $quote */
        $quote = $this->quoteRepository->getActive($cartId);

        return $quote->getMwRwrdpointsAmnt();
    }

    /**
     * {@inheritdoc}
     */
    public function saveBalance(\MageWorx\RewardPoints\Api\Data\CustomerBalanceInterface $customerBalance)
    {
        try {
            $customerId = $customerBalance->getCustomerId();
            $websiteId  = $customerBalance->getWebsiteId();

            /** @var \MageWorx\RewardPoints\Model\CustomerBalance $currentCustomerBalance */
            $currentCustomerBalance = $this->customerBalanceRepository->getByCustomer($customerId, $websiteId);

            /** @var \Magento\Customer\Model\Customer $customer */
            $customer = $this->customerRepository->getById($customerId);

            $website = $this->storeManager->getWebsite($websiteId);

            $pointsDelta = $customerBalance->getPoints() - $currentCustomerBalance->getPoints();

            $transactionData['expiration_period'] = $customerBalance->getExpirationDate();
            $transactionData['website_id']        = $customerBalance->getWebsiteId();
            $transactionData['points_delta']      = $pointsDelta;

            if (empty($transactionData['store_id'])) {
                $transactionData['store_id'] = $this->helperStore->getWebsiteStoreId($website);
            }

            if ($customer->getWebsiteId() == $websiteId || $this->shareConfig->isGlobalScope()) {

                if ($pointsDelta) {
                    $updatePointBalanceObject = $this->dataObjectFactory->create();
                    $updatePointBalanceObject->setData($transactionData);

                    $this->pointTransactionApplier->applyTransaction(
                        \MageWorx\RewardPoints\Model\EventStrategyFactory::MANUAL_UPDATE_EVENT,
                        $customer,
                        $transactionData['store_id'],
                        $updatePointBalanceObject
                    );

                    return $customerBalance;
                } else {

                    if ($customerBalance->getExpirationDate()
                        && $currentCustomerBalance->getId()
                        && $currentCustomerBalance->getPoints()
                    ) {
                        $currentCustomerBalance->setExpirationDate($customerBalance->getExpirationDate());

                        if ($currentCustomerBalance->dataHasChangedFor('expiration_date')) {
                            $this->customerBalanceRepository->save($currentCustomerBalance);

                            return $currentCustomerBalance;
                        }
                    }
                }
            }

            return false;

        } catch (\Exception $e) {
            throw new CouldNotSaveException(__('Could not save reward points balance'));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function applyTransaction(\MageWorx\RewardPoints\Api\Data\PointTransactionInterface $pointTransaction)
    {
        try {
            $data = [
                'customer_id'               => $pointTransaction->getCustomerId(),
                'store_id'                  => $pointTransaction->getStoreId(),
                'points_delta'              => $pointTransaction->getPointsDelta(),
                'is_need_send_notification' => $pointTransaction->getIsNeedSendNotification(),
                'comment'                   => $pointTransaction->getComment()
            ];

            /** @var \Magento\Customer\Model\Customer $customer */
            $customer = $this->customerRepository->getById($data['customer_id']);

            //Single Store Mode
            if (empty($data['store_id'])) {
                $store            = $this->storeManager->getDefaultStoreView();
                $data['store_id'] = $store->getStoreId();
            } else {
                $store = $this->storeManager->getStore($data['store_id']);
            }

            $websiteId = $store->getWebsiteId();

            if (!$pointTransaction->hasData('expiration_period')) {
                $data['expiration_period'] = $this->helperData->getDefaultExpirationPeriod($websiteId);
            } elseif ($pointTransaction->getExpirationPeriod() === null) {
                $data['expiration_period'] = null;
            } else {
                $data['expiration_period'] = $pointTransaction->getExpirationPeriod();
            }

            if ($customer->getWebsiteId() == $websiteId || $this->shareConfig->isGlobalScope()) {

                if ($data['points_delta']) {

                    $updatePointBalanceObject = $this->dataObjectFactory->create();
                    $updatePointBalanceObject->setData($data);

                    $this->pointTransactionApplier->applyTransaction(
                        \MageWorx\RewardPoints\Model\EventStrategyFactory::MANUAL_UPDATE_EVENT,
                        $customer,
                        $data['store_id'],
                        $updatePointBalanceObject
                    );
                } elseif ($data['expiration_period'] !== null) {

                    $customerBalance = $this->customerBalanceRepository->getByCustomer($customer->getId(), $websiteId);

                    if ($customerBalance->getId() && $customerBalance->getPoints()) {

                        $expirationDate = $this->expirationDateHelper->getExpirationDateForBalance(
                            $customerBalance,
                            $data['expiration_period']
                        );

                        $customerBalance->setExpirationDate($expirationDate);
                        $this->customerBalanceRepository->save($customerBalance);
                    }
                } else {
                    return false;
                }
            } else {
                return false;
            }

            if (empty($customerBalance)) {
                $customerBalance = $this->customerBalanceRepository->getByCustomer($customer->getId(), $websiteId);
            }

            return $customerBalance;

        } catch (\Exception $e) {
            throw new CouldNotSaveException(__('Could not save reward points balance'));
        }
    }
}
