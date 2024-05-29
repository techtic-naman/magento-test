<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace MageWorx\SocialProofBase\Model;

use Magento\Catalog\Model\Product\Attribute\Source\Status as SourceProductStatus;
use Magento\CatalogRule\Model\Rule\Condition\Combine as ConditionCombine;
use Magento\CatalogRule\Model\Rule\Condition\CombineFactory as ConditionCombineFactory;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DataObject;
use Magento\Framework\DataObjectFactory;
use Magento\Framework\Event\ManagerInterface as EventManagerInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Serialize\Serializer\Json as SerializerJson;
use Magento\Sales\Api\Data\OrderItemInterface;
use Magento\Sales\Model\Order as ModelOrder;
use Magento\Sales\Model\ResourceModel\Order\Item\Collection as OrderItemCollection;
use Magento\Sales\Model\ResourceModel\Order\Item\CollectionFactory as OrderItemCollectionFactory;
use Magento\Store\Model\StoreManagerInterface;
use MageWorx\SocialProofBase\Api\ConverterInterface;
use MageWorx\SocialProofBase\Api\Data\CampaignInterface;
use MageWorx\SocialProofBase\Api\MobileDetectorAdapterInterface;
use MageWorx\SocialProofBase\Model\ResourceModel\Campaign\Collection;
use MageWorx\SocialProofBase\Model\Source\Campaign\DisplayOn as DisplayOnOptions;
use MageWorx\SocialProofBase\Model\Source\Campaign\EventType as EventTypeOptions;
use MageWorx\SocialProofBase\Model\Source\Campaign\Products\AssignType as ProductsAssignTypeOptions;
use MageWorx\SocialProofBase\Model\Source\Campaign\Status as StatusOptions;
use MageWorx\SocialProofBase\Model\ResourceModel\Campaign\CollectionFactory as CampaignCollectionFactory;
use MageWorx\SocialProofBase\Model\ConverterFactory;
use Magento\Catalog\Model\ResourceModel\Product\Collection as ProductCollection;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;

class FrontendCampaignResolver implements \MageWorx\SocialProofBase\Api\FrontendCampaignResolverInterface
{
    /**
     * Campaign collection factory
     *
     * @var CampaignCollectionFactory
     */
    protected $campaignCollectionFactory;

    /**
     * @var MobileDetectorAdapterInterface
     */
    protected $mobileDetectorAdapter;

    /**
     * @var ConditionCombineFactory
     */
    protected $conditionCombineFactory;

    /**
     * @var SerializerJson
     */
    protected $serializerJson;

    /**
     * @var ConverterInterface[]
     */
    protected $converters = [];

    /**
     * @var ConverterFactory
     */
    protected $converterFactory;

    /**
     * @var DataObjectFactory
     */
    protected $objectFactory;

    /**
     * @var OrderItemCollectionFactory
     */
    protected $orderItemCollectionFactory;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var EventManagerInterface
     */
    protected $eventManager;

    /**
     * @var ResourceConnection
     */
    protected $resourceConnection;

    /**
     * @var ProductCollectionFactory
     */
    protected $productCollectionFactory;

    /**
     * FrontendCampaignResolver constructor.
     *
     * @param CampaignCollectionFactory $campaignCollectionFactory
     * @param MobileDetectorAdapterInterface $mobileDetectorAdapter
     * @param ConditionCombineFactory $conditionCombineFactory
     * @param SerializerJson $serializerJson
     * @param ConverterFactory $converterFactory
     * @param DataObjectFactory $objectFactory
     * @param OrderItemCollectionFactory $orderItemCollectionFactory
     * @param StoreManagerInterface $storeManager
     * @param EventManagerInterface $eventManager
     * @param ResourceConnection $resourceConnection
     * @param ProductCollectionFactory $productCollectionFactory
     */
    public function __construct(
        CampaignCollectionFactory $campaignCollectionFactory,
        MobileDetectorAdapterInterface $mobileDetectorAdapter,
        ConditionCombineFactory $conditionCombineFactory,
        SerializerJson $serializerJson,
        ConverterFactory $converterFactory,
        DataObjectFactory $objectFactory,
        OrderItemCollectionFactory $orderItemCollectionFactory,
        StoreManagerInterface $storeManager,
        EventManagerInterface $eventManager,
        ResourceConnection $resourceConnection,
        ProductCollectionFactory $productCollectionFactory
    ) {
        $this->campaignCollectionFactory  = $campaignCollectionFactory;
        $this->mobileDetectorAdapter      = $mobileDetectorAdapter;
        $this->conditionCombineFactory    = $conditionCombineFactory;
        $this->serializerJson             = $serializerJson;
        $this->converterFactory           = $converterFactory;
        $this->objectFactory              = $objectFactory;
        $this->orderItemCollectionFactory = $orderItemCollectionFactory;
        $this->storeManager               = $storeManager;
        $this->eventManager               = $eventManager;
        $this->resourceConnection         = $resourceConnection;
        $this->productCollectionFactory   = $productCollectionFactory;
    }

    /**
     * @param string $displayMode
     * @param string $pageType
     * @param string $associatedEntityId
     * @param int|null $storeId
     * @param int|null $customerGroupId
     * @param array|null $campaignIds
     * @param bool $excludeCampIds
     * @param array|null $itemIds
     * @param bool $excludeItemIds
     * @return DataObject|null
     * @throws NoSuchEntityException
     */
    public function getCampaign(
        $displayMode,
        $pageType,
        $associatedEntityId,
        $storeId,
        $customerGroupId,
        $campaignIds = null,
        $excludeCampIds = false,
        $itemIds = null,
        $excludeItemIds = true
    ): ?DataObject {
        /** @var Collection $collection */
        $collection = $this->campaignCollectionFactory->create();

        $collection
            ->addStoreFilter($storeId)
            ->addCustomerGroupFilter($customerGroupId)
            ->addPageTypeFilter($pageType)
            ->addAssociatedEntityFilter($associatedEntityId)
            ->addFieldToFilter(CampaignInterface::DISPLAY_MODE, $displayMode)
            ->addFieldToFilter(CampaignInterface::STATUS, StatusOptions::ENABLE)
            ->addFieldToFilter(CampaignInterface::CONTENT, ['neq' => ''])
            ->addFieldToFilter(CampaignInterface::MAX_NUMBER_PER_PAGE, ['gteq' => 1])
            ->setOrder(CampaignInterface::PRIORITY, Collection::SORT_ORDER_ASC);

        if ($this->mobileDetectorAdapter->isMobile()) {
            $collection->addFieldToFilter(CampaignInterface::DISPLAY_ON_MOBILE, 1);
        }

        if ($campaignIds) {
            $collection->addIdsFilter($campaignIds, $excludeCampIds);
        }

        $this->eventManager->dispatch(
            'mageworx_socialproofbase_before_load_frontend_campaign_collection_data',
            ['collection' => $collection]
        );

        $campaigns = $collection->getData();

        if (empty($campaigns)) {
            return null;
        }

        foreach ($campaigns as $campaignData) {

            if ($pageType === DisplayOnOptions::PRODUCT_PAGES
                && !$campaignData[CampaignInterface::RESTRICT_TO_CURRENT_PRODUCT]
                && $campaignData[CampaignInterface::PRODUCTS_ASSIGN_TYPE] == ProductsAssignTypeOptions::BY_CONDITIONS
            ) {
                if (!$this->validateProductByConditions(
                    (int)$associatedEntityId,
                    $campaignData[CampaignInterface::DISPLAY_ON_PRODUCTS_CONDITIONS_SERIALIZED]
                )) {
                    continue;
                }
            }

            if (!$this->hasVarsInTemplate($campaignData)) {
                return $this->objectFactory->create(['data' => $campaignData]);
            }

            if ($campaignData[CampaignInterface::EVENT_TYPE] == EventTypeOptions::RECENT_SALES) {

                if ($pageType === DisplayOnOptions::PRODUCT_PAGES
                    && $campaignData[CampaignInterface::RESTRICT_TO_CURRENT_PRODUCT]
                ) {
                    $orderItem = $this->getOrderItem($campaignData, [(int)$associatedEntityId]);
                } elseif ($itemIds) {
                    $orderItem = $this->getOrderItem($campaignData, $itemIds, $excludeItemIds);
                } else {
                    $orderItem = $this->getOrderItem($campaignData);
                }

                if (empty($orderItem)) {
                    continue;
                }

                $campaignData['orderItem'] = $orderItem;
            } elseif ($campaignData[CampaignInterface::EVENT_TYPE] == EventTypeOptions::VIEWS) {

                $viewedItem = $this->getViewedItem(
                    (int)$associatedEntityId,
                    $campaignData[CampaignInterface::PERIOD],
                    $campaignData[CampaignInterface::MIN_NUMBER_OF_VIEWS]
                );

                if (empty($viewedItem)) {
                    continue;
                }

                $campaignData['viewedItem'] = $viewedItem;
            }

            return $this->objectFactory->create(['data' => $campaignData]);
        }

        return null;
    }

    /**
     * @param int $productId
     * @param string|null $conditionsSerialized
     * @return bool
     */
    protected function validateProductByConditions($productId, $conditionsSerialized): bool
    {
        if (!$conditionsSerialized) {
            return true;
        }

        /** @var ConditionCombine $conditionsObj */
        $conditions = $this->conditionCombineFactory->create();

        $conditions->setPrefix('conditions');
        $conditions->loadArray($this->serializerJson->unserialize($conditionsSerialized));

        return $conditions->validateByEntityId($productId);
    }

    /**
     * @param array $campaignData
     * @return bool
     */
    protected function hasVarsInTemplate($campaignData): bool
    {
        $eventType = $campaignData[CampaignInterface::EVENT_TYPE];
        $template  = $campaignData[CampaignInterface::CONTENT];

        if (!isset($this->converters[$eventType])) {
            $this->converters[$eventType] = $this->converterFactory->create($eventType);
        }

        foreach ($this->converters[$eventType]->getAllowedVars() as $var) {
            if (strpos($template, "[$var]") !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param array $campaignData
     * @param array|null $productIds
     * @param bool $exclude
     * @return DataObject|null
     * @throws NoSuchEntityException
     */
    protected function getOrderItem($campaignData, $productIds = null, $exclude = false): ?DataObject
    {
        $items = $this->getOrderItems($campaignData[CampaignInterface::PERIOD], $productIds, $exclude);

        if (empty($items)) {
            return null;
        }

        $disabledProductIds   = $this->getDisabledProductIds($items);
        $conditionsSerialized = $campaignData[CampaignInterface::RESTRICTION_CONDITIONS_SERIALIZED];

        if (!$conditionsSerialized) {

            foreach ($items as $itemData) {
                if (in_array($itemData[OrderItemInterface::PRODUCT_ID], $disabledProductIds)) {
                    continue;
                }

                return $this->objectFactory->create(['data' => $itemData]);
            }

            return null;
        }

        /** @var ConditionCombine $conditionsObj */
        $conditions = $this->conditionCombineFactory->create();

        $conditions->setPrefix('conditions');
        $conditions->loadArray($this->serializerJson->unserialize($conditionsSerialized));

        foreach ($items as $itemData) {
            if (in_array($itemData[OrderItemInterface::PRODUCT_ID], $disabledProductIds)) {
                continue;
            }

            if ($conditions->validateByEntityId($itemData[OrderItemInterface::PRODUCT_ID])) {
                return $this->objectFactory->create(['data' => $itemData]);
            }
        }

        return null;
    }

    /**
     * @param array $orderItems
     * @return array
     */
    protected function getDisabledProductIds($orderItems): array
    {
        $productIds = array_column($orderItems, OrderItemInterface::PRODUCT_ID);

        /** @var ProductCollection $collection */
        $collection = $this->productCollectionFactory->create();
        $collection
            ->addAttributeToSelect('entity_id')
            ->addIdFilter($productIds)
            ->addAttributeToFilter(
                \Magento\Catalog\Api\Data\ProductInterface::STATUS,
                SourceProductStatus::STATUS_DISABLED
            );

        $ids = array_column($collection->getData(), 'entity_id');

        return $collection->isEnabledFlat() ? array_diff($productIds, $ids) : $ids;
    }

    /**
     * @param int|null $period
     * @param array|null $productIds
     * @param bool $exclude
     * @return array
     * @throws NoSuchEntityException
     */
    protected function getOrderItems($period = null, $productIds = null, $exclude = false): array
    {
        /** @var OrderItemCollection $collection */
        $collection = $this->orderItemCollectionFactory->create();
        $collection
            ->addFieldToSelect(
                [OrderItemInterface::PRODUCT_ID, OrderItemInterface::NAME, OrderItemInterface::CREATED_AT]
            )
            ->addFieldToFilter('main_table.' . OrderItemInterface::PARENT_ITEM_ID, ['null' => true])
            ->addFieldToFilter('main_table.' . OrderItemInterface::STORE_ID, $this->storeManager->getStore()->getId())
            ->join(
                ['orders' => 'sales_order'],
                'main_table.' . OrderItemInterface::ORDER_ID . ' = orders.entity_id',
                [ModelOrder::BILLING_ADDRESS_ID]
            )
            ->addOrder('main_table.' . OrderItemInterface::ITEM_ID);

        if ($productIds) {
            $this->filterOrderItemCollectionByProductIds($collection, $productIds, $exclude);
        }

        if ($period > 0) {
            $this->filterOrderItemCollectionByDate($collection, $period);
        }

        $this->groupOrderItemCollectionByProductId($collection);
        $collection->getSelect()->limit(100);

        $this->eventManager->dispatch(
            'mageworx_socialproofbase_before_load_order_item_collection_data',
            ['collection' => $collection]
        );

        return $collection->getData();
    }

    /**
     * @param OrderItemCollection $collection
     * @param array $productIds
     * @param bool $exclude
     */
    protected function filterOrderItemCollectionByProductIds($collection, $productIds, $exclude): void
    {
        if (is_array($productIds) && count($productIds)) {

            if ($exclude) {
                $condition = ['nin' => $productIds];
            } else {
                $condition = ['in' => $productIds];
            }

            $collection->addFieldToFilter('main_table.' . OrderItemInterface::PRODUCT_ID, $condition);
        }
    }

    /**
     * @param OrderItemCollection $collection
     * @param int $period
     */
    protected function filterOrderItemCollectionByDate($collection, $period): void
    {
        $to   = time();
        $from = strtotime("-$period day", $to);

        $collection->addAttributeToFilter(
            'main_table.' . OrderItemInterface::CREATED_AT,
            ['from' => $from, 'to' => $to, 'datetime' => true]
        );
    }

    /**
     * The last order item with this particular product comes up to the result
     *
     * @param OrderItemCollection $collection
     */
    protected function groupOrderItemCollectionByProductId($collection): void
    {
        $select = $collection->getSelect();
        $select
            ->joinLeft(
                ['main_table2' => $collection->getMainTable()],
                'main_table.' . OrderItemInterface::PRODUCT_ID . ' = main_table2.' . OrderItemInterface::PRODUCT_ID .
                ' AND main_table.' . OrderItemInterface::ITEM_ID . ' < main_table2.' . OrderItemInterface::ITEM_ID,
                []
            )
            ->where('main_table2.' . OrderItemInterface::PRODUCT_ID . ' IS NULL');
    }

    /**
     * @param int $productId
     * @param int $period
     * @param int $minNumberOfViews
     * @return DataObject|null
     * @throws NoSuchEntityException
     */
    protected function getViewedItem($productId, $period, $minNumberOfViews): ?DataObject
    {
        $to   = time();
        $from = strtotime("-$period day", $to);

        $table      = $this->resourceConnection->getTableName('report_viewed_product_index');
        $connection = $this->resourceConnection->getConnection();
        $select     = $connection->select();

        $select
            ->from($table)
            ->where($connection->prepareSqlCondition('product_id', $productId))
            ->where($connection->prepareSqlCondition('added_at', ['from' => $from, 'to' => $to, 'datetime' => true]))
            ->where($connection->prepareSqlCondition('store_id', $this->storeManager->getStore()->getId()));

        $countVisitors = count($connection->fetchCol($select));

        if (!$countVisitors || $countVisitors < $minNumberOfViews) {
            return null;
        }

        $itemData = [
            'product_id'     => $productId,
            'period'         => $period,
            'count_visitors' => $countVisitors
        ];

        return $this->objectFactory->create(['data' => $itemData]);
    }
}
