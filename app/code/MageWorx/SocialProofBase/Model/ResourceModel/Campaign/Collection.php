<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace MageWorx\SocialProofBase\Model\ResourceModel\Campaign;

use Magento\Framework\Serialize\Serializer\Json as JsonSerializer;
use MageWorx\SocialProofBase\Model\Campaign;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use MageWorx\SocialProofBase\Api\Data\CampaignInterface;
use Magento\Framework\DB\Select;
use Magento\Framework\Exception\LocalizedException;
use MageWorx\SocialProofBase\Model\ResourceModel\Campaign as CampaignResource;
use Magento\Store\Model\Store as ModelStore;
use MageWorx\SocialProofBase\Model\Source\Campaign\DisplayOn as DisplayOnOptions;
use MageWorx\SocialProofBase\Model\Source\Campaign\Products\AssignType as ProductsAssignTypeOptions;
use MageWorx\SocialProofBase\Model\Source\Campaign\Categories\AssignType as CategoriesAssignTypeOptions;
use MageWorx\SocialProofBase\Model\Source\Campaign\CmsPages\AssignType as CmsPagesAssignTypeOptions;

class Collection extends AbstractCollection
{
    const STORE_TABLE_ALIAS             = 'store_table';
    const CUSTOMER_GROUP_TABLE_ALIAS    = 'customer_group_table';
    const DISPLAY_ON_TABLE_ALIAS        = 'display_on_table';
    const ASSOCIATED_ENTITY_TABLE_ALIAS = 'associated_entity_table';

    const STORE_FIELD             = 'store';
    const CUSTOMER_GROUP_FIELD    = 'customer_group';
    const ASSOCIATED_ENTITY_FIELD = 'associated_entity';

    const STORE_TABLE_COLUMN          = 'store_id';
    const CUSTOMER_GROUP_TABLE_COLUMN = 'customer_group_id';
    const DISPLAY_ON_TABLE_COLUMN     = 'page_type';

    /**
     * ID Field name
     *
     * @var string
     */
    protected $_idFieldName = CampaignInterface::CAMPAIGN_ID;

    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix = 'mageworx_socialproofbase_campaign_collection';

    /**
     * Event object
     *
     * @var string
     */
    protected $_eventObject = 'campaign_collection';

    /**
     * @var array
     */
    protected $mapFields = [
        self::STORE_FIELD             => self::STORE_TABLE_ALIAS . '.' . self::STORE_TABLE_COLUMN,
        self::CUSTOMER_GROUP_FIELD    => self::CUSTOMER_GROUP_TABLE_ALIAS . '.' . self::CUSTOMER_GROUP_TABLE_COLUMN,
        CampaignInterface::DISPLAY_ON => self::DISPLAY_ON_TABLE_ALIAS . '.' . self::DISPLAY_ON_TABLE_COLUMN
    ];

    /**
     * @var array
     */
    protected $associatedEntityIdColumns = [
        DisplayOnOptions::PRODUCT_PAGES  => 'product_id',
        DisplayOnOptions::CATEGORY_PAGES => 'category_id',
        DisplayOnOptions::CMS_PAGES      => 'cms_page_id'
    ];

    /**
     * @var array
     */
    protected $associatedEntityTables = [
        DisplayOnOptions::PRODUCT_PAGES  => CampaignResource::CAMPAIGN_PRODUCT_TABLE,
        DisplayOnOptions::CATEGORY_PAGES => CampaignResource::CAMPAIGN_CATEGORY_TABLE,
        DisplayOnOptions::CMS_PAGES      => CampaignResource::CAMPAIGN_CMS_PAGE_TABLE
    ];

    /**
     * @var array|null
     */
    protected $allowedPageTypes;

    /**
     * @var string|null
     */
    protected $pageTypeFilterValue;

    /**
     * @var JsonSerializer $serializer
     */
    private $serializer;

    /**
     * Collection constructor.
     *
     * @param \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param JsonSerializer $serializer
     * @param \Magento\Framework\DB\Adapter\AdapterInterface|null $connection
     * @param \Magento\Framework\Model\ResourceModel\Db\AbstractDb|null $resource
     */
    public function __construct(
        \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        JsonSerializer $serializer,
        \Magento\Framework\DB\Adapter\AdapterInterface $connection = null,
        \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource = null
    ) {
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $connection, $resource);

        $this->serializer = $serializer;
    }

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct(): void
    {
        $this->_init(Campaign::class, CampaignResource::class);

        foreach ($this->mapFields as $field => $column) {
            $this->_map['fields'][$field] = $column;
        }
    }

    /**
     * Get SQL for get record count.
     * Extra GROUP BY strip added.
     *
     * @return Select
     */
    public function getSelectCountSql(): Select
    {
        $countSelect = parent::getSelectCountSql();
        $countSelect->reset(\Magento\Framework\DB\Select::GROUP);

        return $countSelect;
    }

    /**
     * Add field filter to collection
     *
     * @param array|string $field
     * @param int|string|array|null $condition
     * @return AbstractCollection
     */
    public function addFieldToFilter($field, $condition = null): AbstractCollection
    {
        if ($field === self::STORE_TABLE_COLUMN) {
            return $this->addStoreFilter($condition, false);
        }

        if ($field === self::CUSTOMER_GROUP_TABLE_COLUMN) {
            return $this->addCustomerGroupFilter($condition);
        }

        if ($field === self::DISPLAY_ON_TABLE_COLUMN) {
            return $this->addPageTypeFilter($condition);
        }

        if ($field === 'associated_entity_id') {
            return $this->addAssociatedEntityFilter($condition);
        }

        return parent::addFieldToFilter($field, $condition);
    }

    /**
     * Add filter by store
     *
     * @param int|array|ModelStore $store
     * @param bool $withAdmin
     * @return $this
     */
    public function addStoreFilter($store, $withAdmin = true): Collection
    {
        $this->performAddStoreFilter($store, $withAdmin);

        return $this;
    }

    /**
     * Add filter by customer group
     *
     * @param int|array $customerGroup
     * @return $this
     */
    public function addCustomerGroupFilter($customerGroup): Collection
    {
        $this->performAddCustomerGroupFilter($customerGroup);

        return $this;
    }

    /**
     * Add filter by page type
     *
     * @param string|array $pageType
     * @return $this
     */
    public function addPageTypeFilter($pageType): Collection
    {
        $this->performAddPageTypeFilter($pageType);

        return $this;
    }

    /**
     * Add filter by associated entity Id
     *
     * @param string|array $id
     * @return $this
     */
    public function addAssociatedEntityFilter($id): Collection
    {
        $this->performAddAssociatedEntityFilter($id);

        return $this;
    }

    /**
     * Set filter by Campaign Ids
     *
     * @param array $campaignIds
     * @param bool $exclude
     * @return $this
     */
    public function addIdsFilter($campaignIds, $exclude = false): Collection
    {
        if ($exclude) {
            $condition = ['nin' => $campaignIds];
        } else {
            $condition = ['in' => $campaignIds];
        }

        $this->addFieldToFilter('main_table.' . CampaignInterface::CAMPAIGN_ID, $condition);

        return $this;
    }

    /**
     * Add all associations
     */
    public function addAllAssociations(): void
    {
        $this->addAssociatedStoreViews();
        $this->addAssociatedCustomerGroups();
        $this->addAssociatedPageTypes();
        $this->addAssociatedProducts();
        $this->addAssociatedCategories();
        $this->addAssociatedCmsPages();
    }

    /**
     * Perform adding filter by store
     *
     * @param int|array|ModelStore $store
     * @param bool $withAdmin
     * @return void
     */
    protected function performAddStoreFilter($store, $withAdmin = true): void
    {
        if ($store instanceof ModelStore) {
            $store = [$store->getId()];
        }

        if (!is_array($store)) {
            $store = [$store];
        }

        if ($withAdmin) {
            $store[] = ModelStore::DEFAULT_STORE_ID;
        }

        $this->addFilter(self::STORE_FIELD, ['in' => $store], 'public');
    }

    /**
     * Perform adding filter by customer group
     *
     * @param int|array $customerGroup
     * @return void
     */
    protected function performAddCustomerGroupFilter($customerGroup): void
    {
        if (!is_array($customerGroup)) {
            $customerGroup = [$customerGroup];
        }

        $this->addFilter(self::CUSTOMER_GROUP_FIELD, ['in' => $customerGroup], 'public');
    }

    /**
     * Perform adding filter by page type
     *
     * @param string|array $pageType
     * @return void
     */
    protected function performAddPageTypeFilter($pageType): void
    {
        if (is_string($pageType)) {
            $this->pageTypeFilterValue = $pageType;
            $pageType                  = [$pageType];
        } elseif (is_array($pageType) && count($pageType) === 1) {
            $this->pageTypeFilterValue = current($pageType);
        }

        $condition = $this->getConnection()->quoteInto(
            $this->mapFields[CampaignInterface::DISPLAY_ON] . " IN(?)",
            $pageType
        );

        if (in_array(DisplayOnOptions::PRODUCT_PAGES, $pageType)) {
            $condition .= ' OR ' . CampaignInterface::RESTRICT_TO_CURRENT_PRODUCT . ' = 1';
        }

        $this->addFilter(CampaignInterface::DISPLAY_ON, $condition, 'string');
    }

    /**
     * Perform adding filter by associated entity Id
     *
     * @param string|array $entityId
     * @return void
     */
    protected function performAddAssociatedEntityFilter($entityId): void
    {
        $pageTypeFilter = $this->getFilter(CampaignInterface::DISPLAY_ON);

        if ($pageTypeFilter
            && $this->pageTypeFilterValue
            && in_array($this->pageTypeFilterValue, $this->getAllowedPageTypes())
        ) {
            if (!is_array($entityId)) {
                $entityIds = [$entityId];
            } else {
                $entityIds = $entityId;
            }

            $column    = $this->associatedEntityIdColumns[$this->pageTypeFilterValue];
            $condition = $this->getConnection()->quoteInto(
                self::ASSOCIATED_ENTITY_TABLE_ALIAS . "." . $column . " IN(?)",
                $entityIds
            );


            if ($this->pageTypeFilterValue == DisplayOnOptions::PRODUCT_PAGES) {
                $productAssignTypes = [
                    ProductsAssignTypeOptions::ALL_PRODUCTS,
                    ProductsAssignTypeOptions::BY_CONDITIONS
                ];

                $condition .= " OR " . $this->getConnection()->quoteInto(
                        CampaignInterface::PRODUCTS_ASSIGN_TYPE . " IN (?)",
                        $productAssignTypes
                    );
            } elseif ($this->pageTypeFilterValue == DisplayOnOptions::CATEGORY_PAGES) {
                $condition .= " OR " . $this->getConnection()->quoteInto(
                        CampaignInterface::CATEGORIES_ASSIGN_TYPE . ' = ?',
                        CategoriesAssignTypeOptions::ALL_CATEGORIES
                    );
            } elseif ($this->pageTypeFilterValue == DisplayOnOptions::CMS_PAGES) {
                $condition .= " OR " . $this->getConnection()->quoteInto(
                        CampaignInterface::CMS_PAGES_ASSIGN_TYPE . ' = ?',
                        CmsPagesAssignTypeOptions::ALL_PAGES
                    );
            }

            $this->addFilter(self::ASSOCIATED_ENTITY_FIELD, $condition, 'string');
        }
    }

    /**
     * @return array
     */
    protected function getAllowedPageTypes(): array
    {
        if (!is_array($this->allowedPageTypes)) {
            $this->allowedPageTypes = array_keys($this->associatedEntityIdColumns);
        }

        return $this->allowedPageTypes;
    }

    /**
     * Perform operations before rendering filters
     *
     * @return void
     */
    protected function _renderFiltersBefore(): void
    {
        $this->joinRelationTable(
            CampaignResource::CAMPAIGN_STORE_TABLE,
            self::STORE_TABLE_ALIAS,
            self::STORE_FIELD
        );
        $this->joinRelationTable(
            CampaignResource::CAMPAIGN_CUSTOMER_GROUP_TABLE,
            self::CUSTOMER_GROUP_TABLE_ALIAS,
            self::CUSTOMER_GROUP_FIELD
        );
        $this->joinPageTypeRelationTable(
            CampaignResource::CAMPAIGN_DISPLAY_ON_TABLE,
            self::DISPLAY_ON_TABLE_ALIAS
        );
        $this->joinAssociatedEntityRelationTable(self::ASSOCIATED_ENTITY_TABLE_ALIAS);

        parent::_renderFiltersBefore();
    }

    /**
     * Join relation table if there is corresponding filter
     *
     * @param string $tableName
     * @param string $tableAlias
     * @param string $field
     * @param string $linkField
     * @return void
     */
    protected function joinRelationTable(
        $tableName,
        $tableAlias,
        $field,
        $linkField = CampaignInterface::CAMPAIGN_ID
    ): void {

        if ($this->getFilter($field)) {
            $this->getSelect()
                 ->join(
                     [$tableAlias => $this->getTable($tableName)],
                     'main_table.' . $linkField . ' = ' . $tableAlias . '.' . $linkField,
                     []
                 )->group(
                    'main_table.' . $linkField
                );
        }
    }

    /**
     * Join Page Type relation table if there is page type filter
     *
     * @param string $tableName
     * @param string $tableAlias
     * @param string $linkField
     * @return void
     */
    protected function joinPageTypeRelationTable(
        $tableName,
        $tableAlias,
        $linkField = CampaignInterface::CAMPAIGN_ID
    ): void {

        if ($this->getFilter(CampaignInterface::DISPLAY_ON)) {
            $this->getSelect()
                 ->joinLeft(
                     [$tableAlias => $this->getTable($tableName)],
                     'main_table.' . $linkField . ' = ' . $tableAlias . '.' . $linkField,
                     []
                 )->group(
                    'main_table.' . $linkField
                );
        }
    }

    /**
     * Join Associated Entity relation table if there is Associated Entity filter
     *
     * @param string $tableAlias
     * @param string $linkField
     */
    protected function joinAssociatedEntityRelationTable($tableAlias, $linkField = CampaignInterface::CAMPAIGN_ID): void
    {
        if ($this->getFilter(self::ASSOCIATED_ENTITY_FIELD)) {
            $tableName = $this->associatedEntityTables[$this->pageTypeFilterValue];

            $this->getSelect()
                 ->joinLeft(
                     [$tableAlias => $this->getTable($tableName)],
                     'main_table.' . $linkField . ' = ' . $tableAlias . '.' . $linkField,
                     []
                 )->group(
                    'main_table.' . $linkField
                );
        }
    }

    /**
     * @param string|null $valueField
     * @param string $labelField
     * @param array $additional
     * @return array
     */
    protected function _toOptionArray(
        $valueField = CampaignInterface::CAMPAIGN_ID,
        $labelField = CampaignInterface::NAME,
        $additional = []
    ): array {
        return parent::_toOptionArray($valueField, $labelField, $additional);
    }

    /**
     * @param null $limit
     * @param null $offset
     * @return Select
     * @throws LocalizedException
     */
    protected function getAllIdsSelect($limit = null, $offset = null): Select
    {
        $idsSelect = clone $this->getSelect();
        $idsSelect->reset(Select::ORDER);
        $idsSelect->reset(Select::LIMIT_COUNT);
        $idsSelect->reset(Select::LIMIT_OFFSET);
        $idsSelect->columns($this->getResource()->getIdFieldName(), 'main_table');
        $idsSelect->limit($limit, $offset);

        return $idsSelect;
    }

    /**
     * @return void
     */
    protected function addAssociatedStoreViews(): void
    {
        $this->updateCollectionItemsUsingAssociatedEntities(
            CampaignResource::CAMPAIGN_STORE_TABLE,
            CampaignInterface::STORE_IDS
        );
    }

    /**
     * @return void
     */
    protected function addAssociatedCustomerGroups(): void
    {
        $this->updateCollectionItemsUsingAssociatedEntities(
            CampaignResource::CAMPAIGN_CUSTOMER_GROUP_TABLE,
            CampaignInterface::CUSTOMER_GROUP_IDS
        );
    }

    /**
     * @return void
     */
    protected function addAssociatedProducts(): void
    {
        $this->updateCollectionItemsUsingAssociatedEntities(
            CampaignResource::CAMPAIGN_PRODUCT_TABLE,
            CampaignInterface::PRODUCT_IDS
        );
    }

    /**
     * @return void
     */
    protected function addAssociatedPageTypes(): void
    {
        $this->updateCollectionItemsUsingAssociatedEntities(
            CampaignResource::CAMPAIGN_DISPLAY_ON_TABLE,
            CampaignInterface::DISPLAY_ON
        );
    }

    /**
     * @return void
     */
    protected function addAssociatedCategories(): void
    {
        $this->updateCollectionItemsUsingAssociatedEntities(
            CampaignResource::CAMPAIGN_CATEGORY_TABLE,
            CampaignInterface::CATEGORY_IDS
        );
    }

    /**
     * @return void
     */
    protected function addAssociatedCmsPages(): void
    {
        $this->updateCollectionItemsUsingAssociatedEntities(
            CampaignResource::CAMPAIGN_CMS_PAGE_TABLE,
            CampaignInterface::CMS_PAGE_IDS
        );
    }

    /**
     * @param string $tableName
     * @param string $fieldName
     */
    protected function updateCollectionItemsUsingAssociatedEntities($tableName, $fieldName): void
    {
        $ids = $this->getColumnValues(CampaignInterface::CAMPAIGN_ID);

        if (count($ids)) {
            $data       = [];
            $columnName = ($fieldName == CampaignInterface::DISPLAY_ON) ? 'page_type' : rtrim($fieldName, 's');
            $methodName = 'set' . str_replace('_', '', ucwords($fieldName, '_'));
            $connection = $this->getConnection();
            $select     = $connection->select()
                                     ->from($this->getTable($tableName))
                                     ->where(CampaignInterface::CAMPAIGN_ID . ' IN (?)', $ids);

            $result = $connection->fetchAll($select);

            if ($result) {

                foreach ($result as $row) {
                    $id                      = $row[CampaignInterface::CAMPAIGN_ID];
                    $data[$id][$fieldName][] = $row[$columnName];
                }
            }

            foreach ($this as $item) {
                $itemId = $item->getId();

                if (!empty($data[$itemId])) {
                    $item->{$methodName}($data[$itemId][$fieldName]);
                } else {
                    $item->{$methodName}([]);
                }
            }
        }
    }

    /**
     * Find product attribute in conditions
     *
     * @param string $attributeCode
     * @return Collection
     */
    public function addAttributeInConditionFilter($attributeCode): Collection
    {
        $match = sprintf('%%%s%%', substr($this->serializer->serialize(['attribute' => $attributeCode]), 1, -1));

        $field1      = $this->_getMappedField(CampaignInterface::DISPLAY_ON_PRODUCTS_CONDITIONS_SERIALIZED);
        $productCond = $this->_getConditionSql($field1, ['like' => $match]);

        $field2          = $this->_getMappedField(CampaignInterface::RESTRICTION_CONDITIONS_SERIALIZED);
        $restrictionCond = $this->_getConditionSql($field2, ['like' => $match]);

        $this->getSelect()->where(
            sprintf('(%s OR %s)', $productCond, $restrictionCond),
            null,
            Select::TYPE_CONDITION
        );

        return $this;
    }
}
