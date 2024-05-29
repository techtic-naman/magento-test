<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\RewardPoints\Model\Rule;

use MageWorx\RewardPoints\Model\ResourceModel\Rule\Collection;
use MageWorx\RewardPoints\Model\Rule;

/**
 * Class DataProvider
 */
class DataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{
    /**
     * @var Collection
     */
    protected $collection;

    /**
     * @var array
     */
    protected $loadedData;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \Magento\SalesRule\Model\Rule\Metadata\ValueProvider
     */
    protected $metadataValueProvider;

    /**
     * DataProvider constructor.
     *
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param \MageWorx\RewardPoints\Model\ResourceModel\Rule\CollectionFactory $collectionFactory
     * @param \Magento\Framework\Registry $registry
     * @param Metadata\ValueProvider $metadataValueProvider
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        \MageWorx\RewardPoints\Model\ResourceModel\Rule\CollectionFactory $collectionFactory,
        \Magento\Framework\Registry $registry,
        \MageWorx\RewardPoints\Model\Rule\Metadata\ValueProvider $metadataValueProvider,
        array $meta = [],
        array $data = []
    ) {
        $this->collection            = $collectionFactory->create();
        $this->registry              = $registry;
        $this->metadataValueProvider = $metadataValueProvider;
        $meta                        = array_replace_recursive($this->getMetadataValues(), $meta);
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }

    /**
     * Get metadata values
     *
     * @return array
     */
    protected function getMetadataValues()
    {
        $rule = $this->registry->registry(\MageWorx\RewardPoints\Model\RegistryConstants::CURRENT_REWARD_RULE);

        return $this->metadataValueProvider->getMetadataValues($rule);
    }

    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        if (isset($this->loadedData)) {
            return $this->loadedData;
        }
        $items = $this->collection->getItems();
        /** @var Rule $rule */
        foreach ($items as $rule) {

            $rule->load($rule->getId());
            $rule->setPointsAmount($rule->getPointsAmount() * 1);
            $rule->setDiscountQty($rule->getDiscountQty() * 1);

            $this->loadedData[$rule->getId()] = $rule->getData();
        }

        return $this->loadedData;
    }
}
