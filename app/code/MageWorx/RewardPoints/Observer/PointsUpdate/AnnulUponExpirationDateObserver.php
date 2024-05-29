<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\RewardPoints\Observer\PointsUpdate;

class AnnulUponExpirationDateObserver
{
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \MageWorx\RewardPoints\Helper\Data
     */
    protected $helperData;

    /**
     * @var \MageWorx\RewardPoints\Helper\Store
     */
    protected $helperStore;

    /**
     * @var \MageWorx\RewardPoints\Model\PointTransactionApplier
     */
    protected $pointTransactionApplier;

    /**
     * @var \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory
     */
    protected $customerCollectionFactory;

    /**
     * @var \MageWorx\RewardPoints\Model\ResourceModel\CustomerBalance\CollectionFactory
     */
    protected $customerBalanceCollectionFactory;

    /**
     * @var \MageWorx\RewardPoints\Model\PointTransactionRepository
     */
    protected $pointTransactionRepository;

    /**
     * @var \Magento\Customer\Api\Data\CustomerInterface[]
     */
    protected $loadedCustomers = [];

    /**
     * @var string
     */
    protected $eventCode = \MageWorx\RewardPoints\Model\EventStrategyFactory::EXPIRE_DATE_EVENT;

    /**
     * AnnulUponExpirationDateObserver constructor.
     *
     * @param \MageWorx\RewardPoints\Model\ResourceModel\CustomerBalance\CollectionFactory $balanceCollectionFactory
     * @param \MageWorx\RewardPoints\Model\PointTransactionRepository $pointTransactionRepository
     * @param \MageWorx\RewardPoints\Helper\Data $helperData
     * @param \MageWorx\RewardPoints\Helper\Store $helperStore
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \MageWorx\RewardPoints\Model\PointTransactionApplier $pointTransactionApplier
     * @param \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory $customerCollectionFactory
     */
    public function __construct(
        \MageWorx\RewardPoints\Model\ResourceModel\CustomerBalance\CollectionFactory $balanceCollectionFactory,
        \MageWorx\RewardPoints\Model\PointTransactionRepository $pointTransactionRepository,
        \MageWorx\RewardPoints\Helper\Data $helperData,
        \MageWorx\RewardPoints\Helper\Store $helperStore,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \MageWorx\RewardPoints\Model\PointTransactionApplier $pointTransactionApplier,
        \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory $customerCollectionFactory
    ) {
        $this->customerBalanceCollectionFactory = $balanceCollectionFactory;
        $this->pointTransactionRepository       = $pointTransactionRepository;
        $this->helperData                       = $helperData;
        $this->helperStore                      = $helperStore;
        $this->storeManager                     = $storeManager;
        $this->pointTransactionApplier          = $pointTransactionApplier;
        $this->customerCollectionFactory        = $customerCollectionFactory;
    }

    /**
     * @return $this
     * @throws \Exception
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function annulPoints()
    {
        $websites = $this->storeManager->getWebsites();

        foreach ($websites as $websiteId => $website) {

            if (!$this->helperData->isEnableExpirationDate($websiteId)) {
                continue;
            }

            $customerBalanceCollection = $this->customerBalanceCollectionFactory->create();
            $customerBalanceCollection->addWebsiteFilter($websiteId);
            $customerBalanceCollection->addExpiredDateFilter();
            $customerBalanceCollection->addPositivePointBalanceFilter();

            $balanceData = $customerBalanceCollection->toArray();

            if (!$balanceData) {
                continue;
            }

            $customerIds  = array_column($balanceData['items'], 'customer_id');
            $customerList = $this->getCustomers($customerIds);

            foreach ($customerBalanceCollection as $customerBalance) {
                if (empty($customerList[$customerBalance->getCustomerId()])) {
                    continue;
                }

                $customer = $customerList[$customerBalance->getCustomerId()];

                if ($customer->getWebsiteId() !== $websiteId) {
                    $customer->setWebsiteId($websiteId);
                    $customer->setStoreId($this->helperStore->getWebsiteStoreId($website));
                }

                $this->pointTransactionApplier->applyTransaction(
                    $this->eventCode,
                    $customer,
                    $customer->getStoreId(),
                    $customer
                );
            }
        }

        return $this;
    }

    /**
     * @param array $needIds
     * @return array|\Magento\Customer\Api\Data\CustomerInterface[]
     */
    protected function getCustomers(array $needIds)
    {
        $loadedCustomerIds = array_keys($this->loadedCustomers);
        $idsForLoad        = array_diff($needIds, $loadedCustomerIds);

        if ($idsForLoad) {
            /** @var \Magento\Customer\Model\ResourceModel\Customer\Collection $collection */
            $collection = $this->customerCollectionFactory->create();
            $collection->addFieldToFilter('entity_id', ['in' => $idsForLoad]);

            $customers = [];

            foreach ($collection as $customer) {
                $customers[$customer->getId()] = $customer;
            }

            $this->loadedCustomers = $this->loadedCustomers + $customers;
        }

        return $this->loadedCustomers;
    }
}