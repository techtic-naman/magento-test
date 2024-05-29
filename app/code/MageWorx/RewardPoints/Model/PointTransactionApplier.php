<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\RewardPoints\Model;

class PointTransactionApplier
{
    /**
     * @var \MageWorx\RewardPoints\Model\PointTransactionFactory
     */
    protected $pointTransactionFactory;

    /**
     * @var \MageWorx\RewardPoints\Api\PointTransactionRepositoryInterface
     */
    protected $pointTransactionRepository;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \MageWorx\RewardPoints\Helper\Data
     */
    protected $helperData;

    /**
     * @var CustomerRepositoryInterface
     */
    protected $customerRepository;

    /**
     * @var \MageWorx\RewardPoints\Model\EventStrategyFactory
     */
    protected $eventStrategyFactory;

    /**
     * @var \MageWorx\RewardPoints\Model\Source\Event
     */
    protected $ruleEventOptions;

    /**
     * @var \MageWorx\RewardPoints\Model\ResourceModel\Rule\CollectionFactory
     */
    protected $ruleCollectionFactory;

    /**
     * PointTransactionApplier constructor.
     *
     * @param \MageWorx\RewardPoints\Helper\Data $helperData
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param PointTransactionFactory $pointTransactionFactory
     * @param ResourceModel\PointTransaction $pointTransactionRepository
     * @param ResourceModel\Rule\CollectionFactory $ruleCollectionFactory
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
     * @param EventStrategyFactory $eventStrategyFactory
     * @param Source\Event $ruleEventOptions
     */
    public function __construct(
        \MageWorx\RewardPoints\Helper\Data $helperData,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \MageWorx\RewardPoints\Model\PointTransactionFactory $pointTransactionFactory,
        \MageWorx\RewardPoints\Model\ResourceModel\PointTransaction $pointTransactionRepository,
        \MageWorx\RewardPoints\Model\ResourceModel\Rule\CollectionFactory $ruleCollectionFactory,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \MageWorx\RewardPoints\Model\EventStrategyFactory $eventStrategyFactory,
        \MageWorx\RewardPoints\Model\Source\Event $ruleEventOptions
    ) {
        $this->helperData                 = $helperData;
        $this->storeManager               = $storeManager;
        $this->pointTransactionFactory    = $pointTransactionFactory;
        $this->pointTransactionRepository = $pointTransactionRepository;
        $this->ruleCollectionFactory      = $ruleCollectionFactory;
        $this->customerRepository         = $customerRepository;
        $this->eventStrategyFactory       = $eventStrategyFactory;
        $this->ruleEventOptions           = $ruleEventOptions;
    }

    /**
     * @param string $eventCode
     * @param \Magento\Customer\Api\Data\CustomerInterface $customer
     * @param int $storeId
     * @param \Magento\Framework\DataObject $eventEntity
     * @param array $ruleData
     * @param bool $needCalculateRules
     * @throws \Exception
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     */
    public function applyTransaction(
        $eventCode,
        $customer,
        $storeId,
        $eventEntity = null,
        array $ruleData = [],
        $needCalculateRules = true
    ) {
        $ruleIds = array_keys($ruleData);

        $websiteId       = $this->storeManager->getStore($storeId)->getWebsiteId();
        $customerGroupId = $customer->getGroupId();

        if ($this->getIsRuleEvent($eventCode)) {

            $ruleCollection = $this->getRuleCollection(
                $websiteId,
                $customerGroupId,
                $eventCode,
                $ruleIds,
                $needCalculateRules
            );

            if ($needCalculateRules) {
                /** @var \MageWorx\RewardPoints\Model\Rule $rule */
                foreach ($ruleCollection as $rule) {

                    $this->applyOneTransaction(
                        $eventCode,
                        $websiteId,
                        $customer,
                        $storeId,
                        $eventEntity,
                        $rule,
                        $rule->getRuleId()
                    );

                    if ($rule->getStopRulesProcessing()) {
                        break;
                    }
                }
            } else {
                // For this case we don't use the validation and recalculation by rule but use the rule for some static settings.
                foreach ($ruleData as $ruleId => $rulePoints) {

                    /** @var \MageWorx\RewardPoints\Model\Rule|null $rule */
                    $rule = $ruleCollection->getItemById($ruleId);
                    $this->applyOneTransaction(
                        $eventCode,
                        $websiteId,
                        $customer,
                        $storeId,
                        $eventEntity,
                        $rule,
                        $ruleId
                    );
                }
            }
        } else {
            $this->applyOneTransaction($eventCode, $websiteId, $customer, $storeId, $eventEntity);
        }
    }

    /**
     * @param string $eventCode
     * @param int $websiteId
     * @param \Magento\Customer\Api\Data\CustomerInterface $customer
     * @param int|null $storeId
     * @param \Magento\Framework\DataObject $eventEntity
     * @param \MageWorx\RewardPoints\Api\Data\RuleInterface|null $rule
     * @param int|null $ruleId
     */
    protected function applyOneTransaction(
        $eventCode,
        $websiteId,
        $customer,
        $storeId,
        $eventEntity = null,
        $rule = null,
        $ruleId = null
    ) {

        /** @var \MageWorx\RewardPoints\Model\EventStrategy\AbstractRuleStrategy $eventStrategy */
        $eventStrategy = $this->eventStrategyFactory->create($eventCode);

        $eventStrategy->setEntity($eventEntity);
        $eventStrategy->setStoreId($storeId);
        $eventStrategy->setWebsiteId($websiteId);
        $eventStrategy->setRule($rule);
        $eventStrategy->setRuleId($ruleId);

        if ($eventStrategy->canAddPoints()) {

            /** @var \MageWorx\RewardPoints\Model\PointTransaction $pointTransaction */
            $pointTransaction = $this->pointTransactionFactory->create();

            $pointTransaction
                ->setWebsiteId($websiteId)
                ->setCustomerId($customer->getId())
                ->setStoreId($storeId)
                ->setPointsDelta($eventStrategy->getPoints())
                ->setEventCode($eventStrategy->getEventCode())
                ->addEventData($eventStrategy->getEventData())
                ->setEmailTemplateId($eventStrategy->getEmailTemplateId())
                ->setUseImmediateSending($eventStrategy->useImmediateSending())
                ->setRuleId($eventStrategy->getRuleId())
                ->setEntityId($eventStrategy->getEntityId())
                ->setExpirationPeriod($eventStrategy->getExpirationPeriod())
                ->setComment($eventStrategy->getComment());

            if (!$this->helperData->isEnableForCustomer($customer, $storeId)) {
                $pointTransaction->setIsNeedSendNotification(false);
            } else {
                $pointTransaction->setIsNeedSendNotification($eventStrategy->getIsPossibleSendNotification());
            }

            $this->pointTransactionRepository->save($pointTransaction);
            $eventStrategy->processAfterTransactionSave($pointTransaction);
        }
    }

    /**
     * @param int $websiteId
     * @param int $customerGroupId
     * @param string $eventCode
     * @param array $ruleIds
     * @param bool $needCalculateRules
     * @return ResourceModel\Rule\Collection
     */
    protected function getRuleCollection($websiteId, $customerGroupId, $eventCode, $ruleIds, $needCalculateRules)
    {
        $ruleCollection = $this->ruleCollectionFactory->create();

        //Place order
        if ($ruleIds) {

            if ($needCalculateRules) {
                $ruleCollection->addWebsiteGroupDateFilter($websiteId, $customerGroupId);
                $ruleCollection->addIsActiveFilter();
            }

            $ruleCollection->addFieldToFilter('main_table.rule_id', ['in' => $ruleIds]);
        } else {
            $ruleCollection->addWebsiteGroupDateFilter($websiteId, $customerGroupId);
            $ruleCollection->addIsActiveFilter();
            $ruleCollection->addEventFilter($eventCode);
        }

        $ruleCollection->addSortOrder();

        return $ruleCollection;
    }

    /**
     * @param string $eventCode
     * @return bool
     */
    protected function getIsRuleEvent($eventCode)
    {
        return in_array($eventCode, array_keys($this->ruleEventOptions->toArray()));
    }
}
