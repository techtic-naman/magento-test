<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types = 1);

namespace MageWorx\ReviewAIBase\Ui\Component\ReviewSummary;

use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\SearchCriteriaBuilder;
use Magento\Framework\Api\SortOrderBuilder;
use Magento\Framework\App\RequestInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Ui\DataProvider\AbstractDataProvider;
use MageWorx\ReviewAIBase\Model\ResourceModel\ReviewSummary\Grid\Collection;
use MageWorx\ReviewAIBase\Model\ResourceModel\ReviewSummary\Grid\CollectionFactory;

class DataProvider extends AbstractDataProvider
{
    const LISTING_NAME = 'review_summary_listing_data_source';

    protected SearchCriteriaBuilder $searchCriteriaBuilder;
    protected FilterBuilder         $filterBuilder;
    protected SortOrderBuilder      $sortOrderBuilder;
    protected StoreManagerInterface $storeManager;
    protected RequestInterface      $request;

    protected array $addFieldStrategies;
    protected array $addFilterStrategies;

    /**
     * @var null|Collection
     */
    protected $collection;

    /**
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param FilterBuilder $filterBuilder
     * @param SortOrderBuilder $sortOrderBuilder
     * @param CollectionFactory $collectionFactory
     * @param StoreManagerInterface $storeManager
     * @param RequestInterface $request
     * @param array $addFieldStrategies
     * @param array $addFilterStrategies
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        string                $name,
        string                $primaryFieldName,
        string                $requestFieldName,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        FilterBuilder         $filterBuilder,
        SortOrderBuilder      $sortOrderBuilder,
        CollectionFactory     $collectionFactory,
        StoreManagerInterface $storeManager,
        RequestInterface      $request,
        array                 $addFieldStrategies = [],
        array                 $addFilterStrategies = [],
        array                 $meta = [],
        array                 $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->filterBuilder         = $filterBuilder;
        $this->sortOrderBuilder      = $sortOrderBuilder;
        $this->collection            = $collectionFactory->create();
        $this->storeManager          = $storeManager;
        $this->request               = $request;
        $this->addFieldStrategies    = $addFieldStrategies;
        $this->addFilterStrategies   = $addFilterStrategies;
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        $storeId = (int)$this->request->getParam('store_id');

        if (!$storeId) {
            $storeId = (int)$this->storeManager->getDefaultStoreView()->getId();
        }

        $this->getCollection()->addStoreFilterToJoin($storeId);

        if (!$this->getCollection()->isLoaded()) {
            $this->getCollection()
                 ->load();
        }
        $items = $this->getCollection()->toArray();
        if (empty($items['items'])) {
            return [
                'totalRecords' => 0,
                'items'        => []
            ];
        }

        $indexedItems = [];
        $size         = $this->getCollection()->getSize();
        if ($this->getName() == static::LISTING_NAME) {
            $indexedItems = $items['items'];
        } else {
            $isSingleStore = $this->storeManager->isSingleStoreMode();
            foreach ($items['items'] as $item) {
                if (empty($item['entity_id'])) {
                    $size--;
                    continue;
                }
                $item['id_field_name']            = 'entity_id';
                $item['single_store']             = $isSingleStore;
                $indexedItems[$item['entity_id']] = $item;
            }
        }

        $data = [
            'totalRecords' => $size,
            'items'        => $indexedItems,
        ];

        return $data;
    }

    /**
     * @return Collection
     */
    public function getCollection(): Collection
    {
        return $this->collection;
    }

    /**
     * Add field to select
     *
     * @param string|array $field
     * @param string|null $alias
     * @return void
     */
    public function addField($field, $alias = null): void
    {
        if (isset($this->addFieldStrategies[$field])) {
            $this->addFieldStrategies[$field]->addField($this->getCollection(), $field, $alias);
        } else {
            parent::addField($field, $alias);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function addFilter(\Magento\Framework\Api\Filter $filter): void
    {
        if (isset($this->addFilterStrategies[$filter->getField()])) {
            $this->addFilterStrategies[$filter->getField()]
                ->addFilter(
                    $this->getCollection(),
                    $filter->getField(),
                    [$filter->getConditionType() => $filter->getValue()]
                );
        } else {
            parent::addFilter($filter);
        }
    }
}
