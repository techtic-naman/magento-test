<?php
/**
 *
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace MageWorx\RewardPoints\Observer\PointsEarn;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Customer\Model\Config\Share;
use Magento\Store\Model\StoreManagerInterface;
use MageWorx\RewardPoints\Model\PointTransactionApplier;
use MageWorx\RewardPoints\Helper\Store as HelperStore;
use MageWorx\RewardPoints\Helper\Data as HelperData;

class CustomerRegistrationObserver implements ObserverInterface
{
    /**
     * @var HelperData
     */
    protected $helperData;

    /**
     * @var PointTransactionApplier
     */
    protected $pointTransactionApplier;

    /**
     * @var string
     */
    protected $eventCode = \MageWorx\RewardPoints\Model\Rule::CUSTOMER_REGISTRATION_EVENT;

    /**
     * @var Share
     */
    protected $configShare;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var HelperStore
     */
    protected $helperStore;

    /**
     * CustomerRegistrationObserver constructor.
     *
     * @param HelperData $helperData
     * @param PointTransactionApplier $pointTransactionApplier
     * @param Share $configShare
     * @param StoreManagerInterface $storeManager
     * @param HelperStore $helperStore
     */
    public function __construct(
        HelperData $helperData,
        PointTransactionApplier $pointTransactionApplier,
        Share $configShare,
        StoreManagerInterface $storeManager,
        HelperStore $helperStore
    ) {
        $this->helperData              = $helperData;
        $this->pointTransactionApplier = $pointTransactionApplier;
        $this->configShare             = $configShare;
        $this->storeManager            = $storeManager;
        $this->helperStore             = $helperStore;
    }

    /**
     * @param Observer $observer
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute(Observer $observer)
    {
        /** @var \Magento\Customer\Api\Data\CustomerInterface $customer */
        $customer = $observer->getEvent()->getCustomer();

        if ($customer instanceof \Magento\Customer\Api\Data\CustomerInterface) {

            $customerAssignStoreId = $customer->getStoreId();

            if (!$this->helperData->isEnable($customerAssignStoreId)) {
                return;
            }

            foreach ($this->configShare->getSharedWebsiteIds($customer->getWebsiteId()) as $websiteId) {
                $website              = $this->storeManager->getWebsite($websiteId);
                $customerFinalStoreId = $this->helperStore->getWebsiteStoreId($website, $customerAssignStoreId);

                if ($customerFinalStoreId === null) {
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
    }
}
