<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\RewardPoints\Model;

use \MageWorx\RewardPoints\Api\Data\PointTransactionInterface;

/**
 * Reward Point transaction model
 */
class PointTransaction extends \Magento\Framework\Model\AbstractModel implements PointTransactionInterface
{
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \MageWorx\RewardPoints\Api\Data\CustomerBalanceInterface
     */
    protected $customerBalance;

    /**
     * @var \MageWorx\RewardPoints\Api\CustomerBalanceRepositoryInterface
     */
    protected $customerBalanceRepository;

    /**
     * @var \MageWorx\RewardPoints\Helper\Data
     */
    protected $helperData;

    /**
     * @var \Magento\Customer\Api\Data\CustomerInterface|false|null
     */
    protected $customer;

    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    protected $customerRepository;

    /**
     * @var \MageWorx\RewardPoints\Model\PointTransactionEmailSenderFactory
     */
    protected $pointTransactionEmailSenderFactory;

    /**
     * @var \MageWorx\RewardPoints\Model\PointTransactionEmailHolder
     */
    protected $pointTransactionEmailHolder;

    /**
     * @var string
     */
    protected $emailTemplateId;

    /**
     * @var
     */
    protected $useImmediateSending;

    /**
     * @var \MageWorx\RewardPoints\Helper\ExpirationDate
     */
    protected $expirationDateHelper;

    /**
     * @var bool
     * @deprecated
     */
    protected $isPossibleSendNotification = false;

    /**
     * PointTransaction constructor.
     *
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \MageWorx\RewardPoints\Helper\Data $helperData
     * @param ResourceModel\PointTransaction $resource
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \MageWorx\RewardPoints\Api\CustomerBalanceRepositoryInterface $customerBalanceRepository
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
     * @param PointTransactionEmailSenderFactory $pointTransactionEmailSenderFactory
     * @param \MageWorx\RewardPoints\Helper\ExpirationDate $expirationDateHelper
     * @param PointTransactionEmailHolder $pointTransactionEmailHolder
     * @param \Magento\Framework\Data\Collection\AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \MageWorx\RewardPoints\Helper\Data $helperData,
        \MageWorx\RewardPoints\Model\ResourceModel\PointTransaction $resource,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \MageWorx\RewardPoints\Api\CustomerBalanceRepositoryInterface $customerBalanceRepository,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \MageWorx\RewardPoints\Model\PointTransactionEmailSenderFactory $pointTransactionEmailSenderFactory,
        \MageWorx\RewardPoints\Helper\ExpirationDate $expirationDateHelper,
        \MageWorx\RewardPoints\Model\PointTransactionEmailHolder $pointTransactionEmailHolder,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->helperData                         = $helperData;
        $this->storeManager                       = $storeManager;
        $this->customerBalanceRepository          = $customerBalanceRepository;
        $this->customerRepository                 = $customerRepository;
        $this->pointTransactionEmailHolder        = $pointTransactionEmailHolder;
        $this->pointTransactionEmailSenderFactory = $pointTransactionEmailSenderFactory;
        $this->expirationDateHelper               = $expirationDateHelper;

        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\MageWorx\RewardPoints\Model\ResourceModel\PointTransaction::class);
    }

    /**
     * @return int|null
     */
    public function getTransactionId()
    {
        return $this->_getData(PointTransactionInterface::TRANSACTION_ID);
    }

    /**
     * @param int $transactionId
     * @return $this
     */
    public function setTransactionId($transactionId)
    {
        return $this->setData(PointTransactionInterface::TRANSACTION_ID, $transactionId);
    }

    /**
     * @return int|null
     */
    public function getCustomerBalanceId()
    {
        return $this->getData(PointTransactionInterface::CUSTOMER_BALANCE_ID);
    }

    /**
     * @param int|null $customerBalanceId
     * @return $this
     */
    public function setCustomerBalanceId($customerBalanceId)
    {
        return $this->setData(PointTransactionInterface::CUSTOMER_BALANCE_ID, $customerBalanceId);
    }

    /**
     * @return int|null
     */
    public function getCustomerId()
    {
        return $this->getData(PointTransactionInterface::CUSTOMER_ID);
    }

    /**
     * @param int $customerId
     * @return $this
     */
    public function setCustomerId($customerId)
    {
        return $this->setData(PointTransactionInterface::CUSTOMER_ID, $customerId);
    }

    /**
     * @return int|null
     */
    public function getWebsiteId()
    {
        return $this->getData(PointTransactionInterface::WEBSITE_ID);
    }

    /**
     * @param int $websiteId
     * @return $this
     */
    public function setWebsiteId($websiteId)
    {
        return $this->setData(PointTransactionInterface::WEBSITE_ID, $websiteId);
    }

    /**
     * @return int|null
     */
    public function getStoreId()
    {
        return $this->getData(PointTransactionInterface::STORE_ID);
    }

    /**
     * @param int $storeId
     * @return $this
     */
    public function setStoreId($storeId)
    {
        return $this->setData(PointTransactionInterface::STORE_ID, $storeId);
    }

    /**
     * @return double|null
     */
    public function getPointsBalance()
    {
        return $this->getData(PointTransactionInterface::POINTS_BALANCE);
    }

    /**
     * @param double $pointsBalance
     * @return $this
     */
    public function setPointsBalance($pointsBalance)
    {
        return $this->setData(PointTransactionInterface::POINTS_BALANCE, $pointsBalance);
    }

    /**
     * @return double|null
     */
    public function getPointsDelta()
    {
        return $this->getData(PointTransactionInterface::POINTS_DELTA);
    }

    /**
     * @param double $pointsDelta
     * @return $this
     */
    public function setPointsDelta($pointsDelta)
    {
        return $this->setData(PointTransactionInterface::POINTS_DELTA, $pointsDelta);
    }

    /**
     * @return string|null
     */
    public function getEventCode()
    {
        return $this->getData(PointTransactionInterface::EVENT_CODE);
    }

    /**
     * @param string $eventCode
     * @return $this
     */
    public function setEventCode($eventCode)
    {
        return $this->setData(PointTransactionInterface::EVENT_CODE, $eventCode);
    }

    /**
     * @return mixed[]
     */
    public function getEventData()
    {
        $result = [];

        if ($this->hasData(PointTransactionInterface::EVENT_DATA)) {
            $result = $this->_getData(PointTransactionInterface::EVENT_DATA);
        }

        if (!is_array($result)) {
            throw new \UnexpectedValueException('Event data must be an array.');
        }

        return $result;
    }

    /**
     * @param mixed[] $eventData
     * @return $this
     */
    public function setEventData($eventData)
    {
        return $this->setData(PointTransactionInterface::EVENT_DATA, $eventData);
    }

    /**
     * @return int|null
     */
    public function getRuleId()
    {
        return $this->getData(PointTransactionInterface::RULE_ID);
    }

    /**
     * @param int|null $ruleId
     * @return $this
     */
    public function setRuleId($ruleId)
    {
        return $this->setData(PointTransactionInterface::RULE_ID, $ruleId);
    }

    /**
     * @return int|null
     */
    public function getEntityId()
    {
        return $this->getData(PointTransactionInterface::ENTITY_ID);
    }

    /**
     * @param int|null $entityId
     * @return $this
     */
    public function setEntityId($entityId)
    {
        return $this->setData(PointTransactionInterface::ENTITY_ID, $entityId);
    }

    /**
     * @return string
     */
    public function getCreatedAt()
    {
        return $this->getData(PointTransactionInterface::CREATED_AT);
    }

    /**
     * @return bool
     */
    public function getIsNotificationSent()
    {
        return $this->getData(PointTransactionInterface::IS_NOTIFICATION_SENT);
    }

    /**
     * @param bool $flag
     * @return $this
     */
    public function setIsNotificationSent($flag)
    {
        return $this->setData(PointTransactionInterface::IS_NOTIFICATION_SENT, $flag);
    }

    /**
     * {@inheritdoc}
     */
    public function getExpirationPeriod()
    {
        return $this->getData(PointTransactionInterface::EXPIRATION_PERIOD);
    }

    /**
     * {@inheritdoc}
     */
    public function setExpirationPeriod($days)
    {
        return $this->setData(PointTransactionInterface::EXPIRATION_PERIOD, $days);
    }

    /**
     * {@inheritdoc}
     */
    public function getComment()
    {
        return $this->getData(PointTransactionInterface::COMMENT);
    }

    /**
     * {@inheritdoc}
     */
    public function setComment($comment)
    {
        return $this->setData(PointTransactionInterface::COMMENT, $comment);
    }

    /**
     * {@inheritdoc}
     */
    public function getIsNeedSendNotification()
    {
        return $this->getData(PointTransactionInterface::IS_NEED_SEND_NOTIFICATION);
    }

    /**
     * {@inheritdoc}
     */
    public function setIsNeedSendNotification($flag)
    {
        return $this->setData(PointTransactionInterface::IS_NEED_SEND_NOTIFICATION, $flag);
    }

    /**
     * {@inheritdoc}
     */
    public function beforeSave()
    {
        $this->saveCustomerBalance();
        $this->setCustomerBalanceId($this->getCustomerBalance()->getId());
        $this->setPointsBalance($this->getCustomerBalance()->getPoints());
        $this->addRateToEventData();

        return parent::beforeSave();
    }

    /**
     * {@inheritdoc}
     */
    public function afterSave()
    {
        if ($this->getIsNeedSendNotification()) {
            /** @var \MageWorx\RewardPoints\Model\PointTransactionEmailSender $emailSender */
            $emailSender = $this->pointTransactionEmailSenderFactory->create();

            if ($this->getUseImmediateSending()) {
                $emailSender->sendPointTransactionEmail($this, $this->getCustomer());
            } else {
                $this->pointTransactionEmailHolder->addEmailSendingData($emailSender, $this, $this->getCustomer());
            }
        }

        return parent::afterSave();
    }

    /**
     * @return $this
     * @throws CouldNotSaveException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function saveCustomerBalance()
    {
        if ($this->getWebsiteId()) {
            $websiteId = $this->getWebsiteId();
        } else {
            $websiteId = $this->storeManager->getStore($this->getStoreId())->getWebsiteId();
            $this->setWebsiteId($websiteId);
        }

        $this->customerBalance = $this->getCustomerBalance();

        $expirationDate = $this->expirationDateHelper->getExpirationDateForBalance(
            $this->customerBalance,
            $this->getExpirationPeriod()
        );

        $this->customerBalance
            ->setCustomerId($this->getCustomerId())
            ->setWebsiteId($websiteId)
            ->setPoints($this->customerBalance->getPoints() + $this->getPointsDelta())
            ->setExpirationDate($expirationDate);

        $this->customerBalanceRepository->save($this->customerBalance);

        return $this;
    }

    /**
     * @return \MageWorx\RewardPoints\Api\Data\CustomerBalanceInterface|CustomerBalance|null
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getCustomerBalance()
    {
        if (null === $this->customerBalance) {

            if ($this->getCustomerBalanceId()) {
                $this->customerBalance = $this->customerBalanceRepository->getById($this->getCustomerBalanceId());
            } elseif ($this->getCustomerId() && $this->getWebsiteId()) {
                $this->customerBalance = $this->customerBalanceRepository->getByCustomer(
                    $this->getCustomerId(),
                    $this->getWebsiteId()
                );
            }
        }

        return $this->customerBalance;
    }

    /**
     * @param string $key
     * @return mixed|null
     */
    public function getEventDataByKey($key)
    {
        $data = $this->getEventData();

        return $data[$key] ?? null;
    }

    /**
     * @param array $data
     * @return $this
     */
    public function addEventData(array $data)
    {
        $this->setData(PointTransactionInterface::EVENT_DATA, array_merge($this->getEventData(), $data));

        return $this;
    }

    /**
     * @param bool $flag
     * @return $this
     * @deprecated
     */
    public function setIsPossibleSendNotification($flag)
    {
        return $this->setIsNeedSendNotification($flag);
    }

    /**
     * @return bool
     * @deprecated
     */
    protected function getIsPossibleSendNotification()
    {
        return $this->getIsNeedSendNotification();
    }

    /**
     * @return string
     */
    public function getEmailTemplateId()
    {
        return $this->emailTemplateId;
    }

    /**
     * @param string $emailTemplateId
     * @return $this
     */
    public function setEmailTemplateId($emailTemplateId)
    {
        $this->emailTemplateId = $emailTemplateId;

        return $this;
    }

    /**
     * @return bool
     */
    public function getUseImmediateSending()
    {
        return $this->useImmediateSending;
    }

    /**
     * @param bool $value
     * @return $this
     */
    public function setUseImmediateSending($value)
    {
        $this->useImmediateSending = $value;

        return $this;
    }

    /**
     * @return \Magento\Customer\Api\Data\CustomerInterface|false
     */
    protected function getCustomer()
    {
        if ($this->customer === null) {

            $this->customer = false;
            if ($this->getCustomerId()) {
                $this->customer = $this->customerRepository->getById($this->getCustomerId());
            }
        }

        return $this->customer;
    }

    /**
     * @return $this
     */
    protected function addRateToEventData()
    {
        $rate = $this->helperData->getPointToCurrencyRate($this->getWebsiteId());
        $this->addEventData([PointTransactionInterface::EVENT_DATA_RATE => $rate]);

        return $this;
    }
}
