<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\RewardPoints\Model\Converter;

use MageWorx\RewardPoints\Api\Data\RuleExtensionInterface;
use Magento\SalesRule\Model\Data\Condition;
use MageWorx\RewardPoints\Api\Data\RuleInterface;
use MageWorx\RewardPoints\Model\Data\Rule as RuleDataModel;
use MageWorx\RewardPoints\Model\Rule;

class ToDataModel
{
    /**
     * @var \Magento\SalesRule\Model\RuleFactory
     */
    protected $ruleFactory;

    /**
     * @var \Magento\SalesRule\Api\Data\RuleInterfaceFactory
     */
    protected $ruleDataFactory;

    /**
     * @var \Magento\SalesRule\Api\Data\ConditionInterfaceFactory
     */
    protected $conditionDataFactory;

    /**
     * @var \Magento\Framework\Reflection\DataObjectProcessor
     */
    protected $dataObjectProcessor;

    /**
     * @var \MageWorx\RewardPoints\Api\Data\RuleLabelInterfaceFactory
     */
    protected $ruleLabelFactory;

    /**
     * @var \Magento\Framework\Serialize\Serializer\Json $serializer
     */
    private $serializer;

    /**
     * @var \MageWorx\RewardPoints\Api\Data\RuleExtensionFactory
     */
    private $extensionFactory;

    /**
     * ToDataModel constructor.
     *
     * @param \MageWorx\RewardPoints\Model\RuleFactory $ruleFactory
     * @param \MageWorx\RewardPoints\Api\Data\RuleInterfaceFactory $ruleDataFactory
     * @param \Magento\SalesRule\Api\Data\ConditionInterfaceFactory $conditionDataFactory
     * @param \MageWorx\RewardPoints\Api\Data\RuleLabelInterfaceFactory $ruleLabelFactory
     * @param \Magento\Framework\Reflection\DataObjectProcessor $dataObjectProcessor
     * @param \Magento\Framework\Serialize\Serializer\Json $serializer
     * @param \MageWorx\RewardPoints\Api\Data\RuleExtensionFactory $extensionFactory
     */
    public function __construct(
        \MageWorx\RewardPoints\Model\RuleFactory $ruleFactory,
        \MageWorx\RewardPoints\Api\Data\RuleInterfaceFactory $ruleDataFactory,
        \Magento\SalesRule\Api\Data\ConditionInterfaceFactory $conditionDataFactory,
        \MageWorx\RewardPoints\Api\Data\RuleLabelInterfaceFactory $ruleLabelFactory,
        \Magento\Framework\Reflection\DataObjectProcessor $dataObjectProcessor,
        \Magento\Framework\Serialize\Serializer\Json $serializer,
        \MageWorx\RewardPoints\Api\Data\RuleExtensionFactory $extensionFactory
    ) {
        $this->ruleFactory          = $ruleFactory;
        $this->ruleDataFactory      = $ruleDataFactory;
        $this->conditionDataFactory = $conditionDataFactory;
        $this->ruleLabelFactory     = $ruleLabelFactory;
        $this->dataObjectProcessor  = $dataObjectProcessor;
        $this->serializer           = $serializer;
        $this->extensionFactory     = $extensionFactory;
    }

    /**
     * Converts Reward Rule model to Reward Rule DTO
     *
     * @param Rule $ruleModel
     * @return RuleDataModel
     */
    public function toDataModel(Rule $ruleModel)
    {
        $modelData = $ruleModel->getData();
        $modelData = $this->convertExtensionAttributesToObject($modelData);

        /** @var \MageWorx\RewardPoints\Model\Data\Rule $dataModel */
        $dataModel = $this->ruleDataFactory->create(['data' => $modelData]);

        $this->mapFields($dataModel, $ruleModel);

        return $dataModel;
    }

    /**
     * @param RuleDataModel $dataModel
     * @return $this
     */
    protected function mapStoreLabels(RuleDataModel $dataModel)
    {
        //translate store labels into objects
        if ($dataModel->getStoreLabels() !== null) {
            $storeLabels = [];
            foreach ($dataModel->getStoreLabels() as $storeId => $storeLabel) {
                $storeLabelObj = $this->ruleLabelFactory->create();
                $storeLabelObj->setStoreId($storeId);
                $storeLabelObj->setStoreLabel($storeLabel);
                $storeLabels[] = $storeLabelObj;
            }
            $dataModel->setStoreLabels($storeLabels);
        }

        return $this;
    }

    /**
     * @param RuleDataModel $dataModel
     * @param Rule $ruleModel
     * @return $this
     */
    protected function mapConditions(RuleDataModel $dataModel, Rule $ruleModel)
    {
        $conditionDataModel  = null;
        $conditionSerialized = $ruleModel->getConditionsSerialized();

        if ($conditionSerialized) {
            $conditionArray     = $this->serializer->unserialize($conditionSerialized);
            $conditionDataModel = $this->arrayToConditionDataModel($conditionArray);
        }
        $dataModel->setCondition($conditionDataModel);

        return $this;
    }

    /**
     * @param RuleDataModel $dataModel
     * @param Rule $ruleModel
     * @return $this
     */
    protected function mapActionConditions(RuleDataModel $dataModel, Rule $ruleModel)
    {
        $actionConditionDataModel  = null;
        $actionConditionSerialized = $ruleModel->getActionsSerialized();
        if ($actionConditionSerialized) {
            $actionConditionArray     = $this->serializer->unserialize($actionConditionSerialized);
            $actionConditionDataModel = $this->arrayToConditionDataModel($actionConditionArray);
        }
        $dataModel->setActionCondition($actionConditionDataModel);

        return $this;
    }

    /**
     * Convert extension attributes of model to object if it is an array
     *
     * @param array $data
     * @return array
     */
    private function convertExtensionAttributesToObject(array $data)
    {
        if (isset($data['extension_attributes']) && is_array($data['extension_attributes'])) {
            /** @var RuleExtensionInterface $attributes */
            $data['extension_attributes'] = $this->extensionFactory->create(['data' => $data['extension_attributes']]);
        }

        return $data;
    }

    /**
     * @param RuleDataModel $dataModel
     * @param Rule $ruleModel
     * @return $this
     */
    protected function mapFields(RuleDataModel $dataModel, Rule $ruleModel)
    {
        $this->mapStoreLabels($dataModel);
        $this->mapConditions($dataModel, $ruleModel);
        $this->mapActionConditions($dataModel, $ruleModel);

        return $this;
    }

    /**
     * Convert recursive array into condition data model
     *
     * @param array $input
     * @return Condition
     */
    public function arrayToConditionDataModel(array $input)
    {
        /** @var \Magento\SalesRule\Model\Data\Condition $conditionDataModel */
        $conditionDataModel = $this->conditionDataFactory->create();
        foreach ($input as $key => $value) {
            switch ($key) {
                case 'attribute':
                    $conditionDataModel->setAttributeName($value);
                    break;
                case 'aggregator':
                    $conditionDataModel->setAggregatorType($value);
                    break;
                case 'conditions':
                    $conditions = [];
                    foreach ($value as $condition) {
                        $conditions[] = $this->arrayToConditionDataModel($condition);
                    }
                    $conditionDataModel->setConditions($conditions);
                    break;
                case 'operator':
                    $conditionDataModel->setOperator($value);
                    break;
                case 'type':
                    $conditionDataModel->setConditionType($value);
                    break;
                case 'value':
                    $conditionDataModel->setValue($value);
                    break;
                default:
            }
        }

        return $conditionDataModel;
    }
}
