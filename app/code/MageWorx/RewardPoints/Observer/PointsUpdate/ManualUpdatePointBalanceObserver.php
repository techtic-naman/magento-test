<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\RewardPoints\Observer\PointsUpdate;

use Magento\Framework\Event\ObserverInterface;

class ManualUpdatePointBalanceObserver implements ObserverInterface
{
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \MageWorx\RewardPoints\Model\PointTransactionApplier
     */
    protected $pointTransactionApplier;

    /**
     * @var \Magento\Framework\DataObjectFactory
     */
    protected $dataObjectFactory;

    /**
     * @var \MageWorx\RewardPoints\Api\CustomerBalanceRepositoryInterface
     */
    protected $customerBalanceRepository;

    /**
     * @var \MageWorx\RewardPoints\Helper\ExpirationDate
     */
    protected $expirationDateHelper;

    /**
     * ManualUpdatePointBalanceObserver constructor.
     *
     * @param \MageWorx\RewardPoints\Api\CustomerBalanceRepositoryInterface $customerBalanceRepository
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \MageWorx\RewardPoints\Model\PointTransactionApplier $pointTransactionApplier
     * @param \Magento\Framework\DataObjectFactory $dataObjectFactory
     * @param \MageWorx\RewardPoints\Helper\ExpirationDate $helperDataConverter
     */
    public function __construct(
        \MageWorx\RewardPoints\Api\CustomerBalanceRepositoryInterface $customerBalanceRepository,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \MageWorx\RewardPoints\Model\PointTransactionApplier $pointTransactionApplier,
        \Magento\Framework\DataObjectFactory $dataObjectFactory,
        \MageWorx\RewardPoints\Helper\ExpirationDate $helperDataConverter
    ) {
        $this->customerBalanceRepository = $customerBalanceRepository;
        $this->storeManager              = $storeManager;
        $this->pointTransactionApplier   = $pointTransactionApplier;
        $this->dataObjectFactory         = $dataObjectFactory;
        $this->expirationDateHelper      = $helperDataConverter;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this|void
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /** @var \Magento\Customer\Api\Data\CustomerInterface $customer */
        $customer = $observer->getEvent()->getCustomer();

        if (!$customer->getId()) {
            return $this;
        }

        $data = $observer->getEvent()->getRequest()->getPost('mageworx_rewardpoints_data');

        //Single Store Mode
        if (empty($data['store_id'])) {
            $data['store_id'] = $this->storeManager->getDefaultStoreView()->getStoreId();
        }

        if (\array_key_exists('expiration_period', $data)) {
            $data['expiration_period'] = $this->expirationDateHelper->convertExpirationPeriodFormValue(
                $data['expiration_period']
            );
        } else {
            $data['expiration_period'] = null;
        }

        if (!empty($data['points_delta'])) {

            $updatePointBalanceObject = $this->dataObjectFactory->create();
            $updatePointBalanceObject->setData($data);

            $this->pointTransactionApplier->applyTransaction(
                \MageWorx\RewardPoints\Model\EventStrategyFactory::MANUAL_UPDATE_EVENT,
                $customer,
                $data['store_id'],
                $updatePointBalanceObject
            );
        } else {

            if ($data['expiration_period'] !== null) {

                $customerBalance = $this->customerBalanceRepository->getByCustomer(
                    $customer->getId(),
                    $this->storeManager->getStore($data['store_id'])->getWebsiteId()
                );

                if ($customerBalance->getId() && $customerBalance->getPoints()) {

                    $expirationDate = $this->expirationDateHelper->getExpirationDateForBalance(
                        $customerBalance,
                        $data['expiration_period']
                    );

                    $customerBalance->setExpirationDate($expirationDate);
                    $this->customerBalanceRepository->save($customerBalance);
                }
            }
        }

        return $this;
    }



}
