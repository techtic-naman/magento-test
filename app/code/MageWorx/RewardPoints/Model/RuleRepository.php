<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\RewardPoints\Model;

/**
 * Reward rule CRUD class
 */
class RuleRepository implements \MageWorx\RewardPoints\Api\RuleRepositoryInterface
{
    /**
     * @var \MageWorx\RewardPoints\Model\RuleFactory
     */
    protected $ruleFactory;

    /**
     * @var \MageWorx\RewardPoints\Api\Data\RuleInterfaceFactory
     */
    protected $ruleDataFactory;

    /**
     * @var \MageWorx\RewardPoints\Model\ResourceModel\Rule
     */
    protected $ruleResource;

    /**
     * @var \Magento\SalesRule\Api\Data\ConditionInterfaceFactory
     */
    protected $conditionDataFactory;

    /**
     * @var \MageWorx\RewardPoints\Model\Converter\ToDataModel
     */
    protected $toDataModelConverter;

    /**
     * @var \MageWorx\RewardPoints\Model\Converter\ToModel
     */
    protected $toModelConverter;

    /**
     * @var \Magento\Framework\Reflection\DataObjectProcessor
     */
    protected $dataObjectProcessor;

    /**
     * @var \MageWorx\RewardPoints\Api\Data\RuleSearchResultInterfaceFactory
     */
    protected $searchResultFactory;

    /**
     * @var \Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface
     */
    protected $extensionAttributesJoinProcessor;

    /**
     * @var \MageWorx\RewardPoints\Model\ResourceModel\Rule\CollectionFactory
     */
    protected $ruleCollectionFactory;

    /**
     * @var \Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface
     */
    private $collectionProcessor;

    /**
     * RuleRepository constructor.
     *
     * @param RuleFactory $ruleFactory
     * @param \MageWorx\RewardPoints\Api\Data\RuleInterfaceFactory $ruleDataFactory
     * @param ResourceModel\Rule $ruleResource
     * @param \Magento\SalesRule\Api\Data\ConditionInterfaceFactory $conditionDataFactory
     * @param Converter\ToDataModel $toDataModelConverter
     * @param Converter\ToModel $toModelConverter
     * @param \MageWorx\RewardPoints\Api\Data\RuleSearchResultInterfaceFactory $searchResultFactory
     * @param \Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface $extensionAttributesJoinProcessor
     * @param ResourceModel\Rule\CollectionFactory $ruleCollectionFactory
     * @param \Magento\Framework\Reflection\DataObjectProcessor $dataObjectProcessor
     * @param \Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface $collectionProcessor
     */
    public function __construct(
        \MageWorx\RewardPoints\Model\RuleFactory $ruleFactory,
        \MageWorx\RewardPoints\Api\Data\RuleInterfaceFactory $ruleDataFactory,
        \MageWorx\RewardPoints\Model\ResourceModel\Rule $ruleResource,
        \Magento\SalesRule\Api\Data\ConditionInterfaceFactory $conditionDataFactory,
        \MageWorx\RewardPoints\Model\Converter\ToDataModel $toDataModelConverter,
        \MageWorx\RewardPoints\Model\Converter\ToModel $toModelConverter,
        \MageWorx\RewardPoints\Api\Data\RuleSearchResultInterfaceFactory $searchResultFactory,
        \Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface $extensionAttributesJoinProcessor,
        \MageWorx\RewardPoints\Model\ResourceModel\Rule\CollectionFactory $ruleCollectionFactory,
        \Magento\Framework\Reflection\DataObjectProcessor $dataObjectProcessor,
        \Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface $collectionProcessor
    ) {
        $this->ruleFactory                      = $ruleFactory;
        $this->ruleDataFactory                  = $ruleDataFactory;
        $this->ruleResource                     = $ruleResource;
        $this->conditionDataFactory             = $conditionDataFactory;
        $this->toDataModelConverter             = $toDataModelConverter;
        $this->toModelConverter                 = $toModelConverter;
        $this->searchResultFactory              = $searchResultFactory;
        $this->extensionAttributesJoinProcessor = $extensionAttributesJoinProcessor;
        $this->ruleCollectionFactory            = $ruleCollectionFactory;
        $this->dataObjectProcessor              = $dataObjectProcessor;
        $this->collectionProcessor              = $collectionProcessor;
    }

    /**
     * {@inheritdoc}
     */
    public function save(\MageWorx\RewardPoints\Api\Data\RuleInterface $rule)
    {
        $model = $this->toModelConverter->toModel($rule);
        $this->ruleResource->save($model);
        $modelId = $model->getId();

        $model = $this->ruleFactory->create();
        $this->ruleResource->load($model, $modelId);
        $model->getStoreLabels();

        return $this->toDataModelConverter->toDataModel($model);
    }

    /**
     * {@inheritdoc}
     */
    public function saveRule(\MageWorx\RewardPoints\Model\Rule $model)
    {
        $this->ruleResource->save($model);
        $modelId = $model->getId();

        $model = $this->ruleFactory->create();
        $this->ruleResource->load($model, $modelId);
        $model->getStoreLabels();

        return $this->toDataModelConverter->toDataModel($model);
    }

    /**
     * {@inheritdoc}
     */
    public function getById($id)
    {
        $model = $this->ruleFactory->create();
        $this->ruleResource->load($model, $id);

        if (!$model->getId()) {
            throw new \Magento\Framework\Exception\NoSuchEntityException();
        }

        $model->getStoreLabels();
        $dataModel = $this->toDataModelConverter->toDataModel($model);

        return $dataModel;
    }

    /**
     * {@inheritdoc}
     */
    public function getRuleById($id)
    {
        $model = $this->ruleFactory->create();
        $this->ruleResource->load($model, $id);

        if (!$model->getId()) {
            throw new \Magento\Framework\Exception\NoSuchEntityException();
        }

        $model->getStoreLabels();

        return $model;
    }

    /**
     * {@inheritdoc}
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria)
    {
        /** @var \MageWorx\RewardPoints\Model\ResourceModel\Rule\Collection $collection */
        $collection        = $this->ruleCollectionFactory->create();
        $ruleInterfaceName = \MageWorx\RewardPoints\Api\Data\RuleInterface::class;
        $this->extensionAttributesJoinProcessor->process($collection, $ruleInterfaceName);

        $this->collectionProcessor->process($searchCriteria, $collection);
        $rules = [];
        /** @var \MageWorx\RewardPoints\Model\Rule $ruleModel */
        foreach ($collection->getItems() as $ruleModel) {
            $this->ruleResource->load($ruleModel, $ruleModel->getId());
            $ruleModel->getStoreLabels();
            $rules[] = $this->toDataModelConverter->toDataModel($ruleModel);
        }

        $searchResults = $this->searchResultFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);
        $searchResults->setItems($rules);
        $searchResults->setTotalCount($collection->getSize());

        return $searchResults;
    }

    /**
     * Delete reward rule by ID.
     *
     * @param int $id
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($id)
    {
        $model = $this->ruleFactory->create();
        $this->ruleResource->load($model, $id);

        if (!$model->getId()) {
            throw new \Magento\Framework\Exception\NoSuchEntityException();
        }
        $this->ruleResource->delete($model);

        return true;
    }
}
