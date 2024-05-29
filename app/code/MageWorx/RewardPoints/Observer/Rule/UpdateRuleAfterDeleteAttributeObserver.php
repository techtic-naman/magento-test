<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\RewardPoints\Observer\Rule;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;
use MageWorx\RewardPoints\Api\RuleRepositoryInterface;
use MageWorx\RewardPoints\Model\Converter\ToDataModel;

class UpdateRuleAfterDeleteAttributeObserver implements ObserverInterface
{
    /**
     * @var \MageWorx\RewardPoints\Api\RuleRepositoryInterface
     */
    protected $ruleRepository;

    /**
     * @var \MageWorx\RewardPoints\Model\Converter\ToDataModel
     */
    protected $toDataModelConverter;

    /**
     * @var \MageWorx\RewardPoints\Model\ResourceModel\Rule\CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;

    /**
     * UpdateRuleAfterDeleteAttributeObserver constructor.
     *
     * @param RuleRepositoryInterface $ruleRepository
     * @param ToDataModel $toDataModelConverter
     * @param \MageWorx\RewardPoints\Model\ResourceModel\Rule\CollectionFactory $collectionFactory
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     */
    public function __construct(
        \MageWorx\RewardPoints\Api\RuleRepositoryInterface $ruleRepository,
        \MageWorx\RewardPoints\Model\Converter\ToDataModel $toDataModelConverter,
        \MageWorx\RewardPoints\Model\ResourceModel\Rule\CollectionFactory $collectionFactory,
        \Magento\Framework\Message\ManagerInterface $messageManager
    ) {
        $this->ruleRepository       = $ruleRepository;
        $this->toDataModelConverter = $toDataModelConverter;
        $this->collectionFactory    = $collectionFactory;
        $this->messageManager       = $messageManager;
    }

    /**
     * After delete attribute check rules that contains deleted attribute
     * If rules was found they will seted to inactive and added notice to admin session
     *
     * @param EventObserver $observer
     * @return $this
     */
    public function execute(EventObserver $observer)
    {
        /** @var \Magento\Catalog\Model\ResourceModel\Eav\Attribute $attribute */
        $attribute = $observer->getEvent()->getAttribute();

        if ($this->getIsNeedCheck($attribute)) {
            $this->checkSalesRulesAvailability($attribute->getAttributeCode());
        }

        return $this;
    }

    /**
     * @param \Magento\Catalog\Model\ResourceModel\Eav\Attribute $attribute
     * @return mixed
     */
    protected function getIsNeedCheck($attribute)
    {
        return $attribute->getIsUsedForPromoRules();
    }

    /**
     * Check rules that contains affected attribute
     * If rules were found they will be set to inactive and notice will be add to admin session
     *
     * @param string $attributeCode
     * @return $this
     */
    protected function checkSalesRulesAvailability($attributeCode)
    {
        /* @var $collection \MageWorx\RewardPoints\Model\ResourceModel\Rule\Collection */
        $collection = $this->collectionFactory->create()->addAttributeInConditionFilter($attributeCode);

        $disabledRulesCount = 0;
        foreach ($collection as $rule) {
            /* @var $rule \MageWorx\RewardPoints\Model\Rule */
            $rule->setIsActive(0);
            $this->removeAttributeFromConditions($rule->getConditions(), $attributeCode);
            $this->removeAttributeFromConditions($rule->getActions(), $attributeCode);

            //we don't use repository here: incorrect conversion for conditions
            $rule->save();
            $disabledRulesCount++;
        }

        if ($disabledRulesCount) {
            $this->messageManager->addWarningMessage(
                __(
                    '%1 Reward Rules based on "%2" attribute have been disabled.',
                    $disabledRulesCount,
                    $attributeCode
                )
            );
        }

        return $this;
    }

    /**
     * Remove catalog attribute condition by attribute code from rule conditions
     *
     * @param \Magento\Rule\Model\Condition\Combine $combine
     * @param string $attributeCode
     * @return void
     */
    protected function removeAttributeFromConditions($combine, $attributeCode)
    {
        $conditions = $combine->getConditions();
        foreach ($conditions as $conditionId => $condition) {
            if ($condition instanceof \Magento\Rule\Model\Condition\Combine) {
                $this->removeAttributeFromConditions($condition, $attributeCode);
            }
            if ($condition instanceof \Magento\SalesRule\Model\Rule\Condition\Product) {
                if ($condition->getAttribute() == $attributeCode) {
                    unset($conditions[$conditionId]);
                }
            }
        }
        $combine->setConditions($conditions);
    }
}
