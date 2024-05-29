<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\RewardPoints\Model\Converter;

use Magento\SalesRule\Model\Data\Condition;
use MageWorx\RewardPoints\Api\Data\RuleInterface;
use MageWorx\RewardPoints\Model\Data\Rule as RuleDataModel;
use MageWorx\RewardPoints\Model\Rule;

/**
 * Class ToModel
 *
 * @todo delete empty
 *
 * @package MageWorx\RewardPoints\Model\Converter
 */
class ToModel
{
    /**
     * @var \MageWorx\RewardPoints\Model\RuleFactory
     */
    protected $ruleFactory;

    /**
     * @var \Magento\Framework\Reflection\DataObjectProcessor
     */
    protected $dataObjectProcessor;

    /**
     * @param \MageWorx\RewardPoints\Model\RuleFactory $ruleFactory
     * @param \Magento\Framework\Reflection\DataObjectProcessor $dataObjectProcessor
     */
    public function __construct(
        \MageWorx\RewardPoints\Model\RuleFactory $ruleFactory,
        \Magento\Framework\Reflection\DataObjectProcessor $dataObjectProcessor
    ) {
        $this->ruleFactory         = $ruleFactory;
        $this->dataObjectProcessor = $dataObjectProcessor;
    }

    /**
     * @param \MageWorx\RewardPoints\Model\Rule $ruleModel
     * @param RuleDataModel $dataModel
     * @return $this
     */
    protected function mapConditions(\MageWorx\RewardPoints\Model\Rule $ruleModel, RuleDataModel $dataModel)
    {
        $condition = $dataModel->getCondition();
        if ($condition) {
            $array = $this->dataModelToArray($condition);
            $ruleModel->getConditions()->setConditions([])->loadArray($array);
        }

        return $this;
    }

    /**
     * @param \MageWorx\RewardPoints\Model\Rule $ruleModel
     * @param RuleDataModel $dataModel
     * @return $this
     */
    protected function mapActionConditions(\MageWorx\RewardPoints\Model\Rule $ruleModel, RuleDataModel $dataModel)
    {
        $condition = $dataModel->getActionCondition();
        if ($condition) {
            $array = $this->dataModelToArray($condition, 'actions');
            $ruleModel->getActions()->setActions([])->loadArray($array, 'actions');
        }

        return $this;
    }

    /**
     * @param Rule $ruleModel
     * @param RuleDataModel $dataModel
     * @return $this
     */
    protected function mapStoreLabels(Rule $ruleModel, RuleDataModel $dataModel)
    {
        //translate store labels object into array
        if ($dataModel->getStoreLabels() !== null) {
            $storeLabels = [];
            /** @var \MageWorx\RewardPoints\Api\Data\RuleLabelInterface $ruleLabel */
            foreach ($dataModel->getStoreLabels() as $ruleLabel) {
                $storeLabels[$ruleLabel->getStoreId()] = $ruleLabel->getStoreLabel();
            }
            $ruleModel->setStoreLabels($storeLabels);
        }

        return $this;
    }

    /**
     * @param \MageWorx\RewardPoints\Model\Rule $ruleModel
     * @param RuleDataModel $dataModel
     * @return $this
     */
    protected function mapFields(\MageWorx\RewardPoints\Model\Rule $ruleModel, RuleDataModel $dataModel)
    {
        $this->mapStoreLabels($ruleModel, $dataModel);
        $this->mapConditions($ruleModel, $dataModel);
        $this->mapActionConditions($ruleModel, $dataModel);

        return $this;
    }

    /**
     * Convert recursive array into condition data model
     *
     * @param Condition $condition
     * @param string $key
     * @return array
     */
    public function dataModelToArray(Condition $condition, $key = 'conditions')
    {
        $output = [
            'type'      => $condition->getConditionType(),
            'value'     => $condition->getValue(),
            'attribute' => $condition->getAttributeName(),
            'operator'  => $condition->getOperator()
        ];

        if ($condition->getConditions()) {
            $conditions = [];
            foreach ($condition->getConditions() as $subCondition) {
                $conditions[] = $this->dataModelToArray($subCondition, $key);
            }
            $output[$key] = $conditions;
        }

        if ($condition->getAggregatorType()) {
            $output['aggregator'] = $condition->getAggregatorType();
        }

        return $output;
    }

    /**
     * @param RuleDataModel $dataModel
     * @return $this|Rule
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\InputException
     */
    public function toModel(RuleDataModel $dataModel)
    {
        $ruleId = $dataModel->getRuleId();

        if ($ruleId) {
            $ruleModel = $this->ruleFactory->create()->load($ruleId);
            if (!$ruleModel->getId()) {
                throw new \Magento\Framework\Exception\NoSuchEntityException();
            }
        } else {
            $ruleModel = $this->ruleFactory->create();
            $dataModel->setFromDate(
                $this->formattingDate($dataModel->getFromDate())
            );
            $dataModel->setToDate(
                $this->formattingDate($dataModel->getToDate())
            );
        }

        $modelData = $ruleModel->getData();

        $data = $this->dataObjectProcessor->buildOutputDataArray(
            $dataModel,
            \MageWorx\RewardPoints\Api\Data\RuleInterface::class
        );

        $mergedData = array_merge($modelData, $data);

        $validateResult = $ruleModel->validateData(new \Magento\Framework\DataObject($mergedData));
        if ($validateResult !== true) {
            $text = '';
            /** @var \Magento\Framework\Phrase $errorMessage */
            foreach ($validateResult as $errorMessage) {
                $text .= $errorMessage->getText() . '; ';
            }
            throw new \Magento\Framework\Exception\InputException(new \Magento\Framework\Phrase($text));
        }

        $ruleModel->setData($mergedData);

        $this->mapFields($ruleModel, $dataModel);

        return $ruleModel;
    }

    /**
     * Convert date to ISO8601
     *
     * @param string|null $date
     * @return string|null
     */
    private function formattingDate($date)
    {
        if ($date) {
            $fromDate = new \DateTime($date);
            $date     = $fromDate->format(\DateTime::ISO8601);
        }

        return $date;
    }
}
