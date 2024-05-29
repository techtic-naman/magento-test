<?php

namespace MageWorx\RewardPoints\Observer\PointsEarn;

use Magento\Customer\Api\Data\CustomerInterface;

class CustomerBirthdayObserver
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
     * @var string
     */
    protected $eventCode = \MageWorx\RewardPoints\Model\Rule::CUSTOMER_BIRTHDAY_EVENT;

    /**
     * CustomerBirthdayObserver constructor.
     *
     * @param \MageWorx\RewardPoints\Helper\Data $helperData
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \MageWorx\RewardPoints\Model\PointTransactionApplier $pointTransactionApplier
     * @param \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory $customerCollectionFactory
     */
    public function __construct(
        \MageWorx\RewardPoints\Helper\Data $helperData,
        \MageWorx\RewardPoints\Helper\Store $helperStore,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \MageWorx\RewardPoints\Model\PointTransactionApplier $pointTransactionApplier,
        \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory $customerCollectionFactory
    ) {
        $this->helperData                = $helperData;
        $this->helperStore               = $helperStore;
        $this->storeManager              = $storeManager;
        $this->pointTransactionApplier   = $pointTransactionApplier;
        $this->customerCollectionFactory = $customerCollectionFactory;
    }

    /**
     * Wee add the reward points for each possible store view
     *
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function addPoints()
    {
        $customers = $this->getCustomers();

        foreach ($customers as $customer) {

            $customerAssignStoreId = $customer->getStoreId();

            foreach ($customer->getSharedWebsiteIds() as $websiteId) {

                $website = $this->storeManager->getWebsite($websiteId);

                $customerFinalStoreId = $this->helperStore->getWebsiteStoreId($website, $customerAssignStoreId);

                if (null === $customerFinalStoreId) {
                    continue;
                }

                if (!$this->helperData->isEnableForCustomer($customer, $customerFinalStoreId)) {
                    continue;
                }

                $customer->setWebsiteId($websiteId);
                $customer->setStoreId($customerFinalStoreId);

                $this->pointTransactionApplier->applyTransaction(
                    $this->eventCode,
                    $customer,
                    $customerFinalStoreId,
                    $customer
                );
            }
        }

        return $this;
    }

    /**
     * @return \Magento\Customer\Model\Customer[]
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function getCustomers()
    {
        $now = (new \DateTime())->format(\Magento\Framework\Stdlib\DateTime::DATE_PHP_FORMAT);
        list($year, $month, $day) = explode('-', $now);

        $collection = $this->customerCollectionFactory->create();
        $collection->addAttributeToFilter(
            CustomerInterface::DOB,
            ['regexp' => '^[0-9]{4}-' . $month . '-' . $day . '$']
        );


        return $collection;
    }
}