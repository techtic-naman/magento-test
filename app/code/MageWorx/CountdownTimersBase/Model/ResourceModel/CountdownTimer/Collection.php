<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace MageWorx\CountdownTimersBase\Model\ResourceModel\CountdownTimer;

use Magento\Framework\DB\Select;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Framework\Serialize\Serializer\Json as JsonSerializer;
use Magento\Framework\Stdlib\DateTime;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Store\Model\Store as ModelStore;
use MageWorx\CountdownTimersBase\Api\Data\CountdownTimerInterface;
use MageWorx\CountdownTimersBase\Model\CountdownTimer;
use MageWorx\CountdownTimersBase\Model\ResourceModel\CountdownTimer as CountdownTimerResource;
use MageWorx\CountdownTimersBase\Model\Source\CountdownTimer\DisplayMode as DisplayModeOptions;
use MageWorx\CountdownTimersBase\Model\Source\CountdownTimer\ProductsAssignType as ProductsAssignTypeOptions;
use Magento\Framework\Exception\LocalizedException;

class Collection extends AbstractCollection
{
    const STORE_TABLE_ALIAS          = 'store_table';
    const CUSTOMER_GROUP_TABLE_ALIAS = 'customer_group_table';
    const PRODUCT_TABLE_ALIAS        = 'product_table';

    const STORE_FIELD          = 'store';
    const CUSTOMER_GROUP_FIELD = 'customer_group';
    const PRODUCT_FIELD        = 'product';

    const STORE_TABLE_COLUMN          = 'store_id';
    const CUSTOMER_GROUP_TABLE_COLUMN = 'customer_group_id';
    const PRODUCT_TABLE_COLUMN        = 'product_id';

    /**
     * ID Field name
     *
     * @var string
     */
    protected $_idFieldName = CountdownTimerInterface::COUNTDOWN_TIMER_ID;

    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix = 'mageworx_countdowntimersbase_countdown_timer_collection';

    /**
     * Event object
     *
     * @var string
     */
    protected $_eventObject = 'countdown_timer_collection';

    /**
     * @var array
     */
    protected $mapFields = [
        self::STORE_FIELD          => self::STORE_TABLE_ALIAS . '.' . self::STORE_TABLE_COLUMN,
        self::CUSTOMER_GROUP_FIELD => self::CUSTOMER_GROUP_TABLE_ALIAS . '.' . self::CUSTOMER_GROUP_TABLE_COLUMN,
        self::PRODUCT_FIELD        => self::PRODUCT_TABLE_ALIAS . '.' . self::PRODUCT_TABLE_COLUMN
    ];

    /**
     * @var TimezoneInterface
     */
    protected $timezone;

    /**
     * @var JsonSerializer
     */
    private $serializer;

    /**
     * Collection constructor.
     *
     * @param \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param TimezoneInterface $timezone
     * @param JsonSerializer $serializer
     * @param \Magento\Framework\DB\Adapter\AdapterInterface|null $connection
     * @param \Magento\Framework\Model\ResourceModel\Db\AbstractDb|null $resource
     */
    public function __construct(
        \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        TimezoneInterface $timezone,
        JsonSerializer $serializer,
        \Magento\Framework\DB\Adapter\AdapterInterface $connection = null,
        \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource = null
    ) {
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $connection, $resource);

        $this->timezone   = $timezone;
        $this->serializer = $serializer;
    }

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct(): void
    {
        $this->_init(CountdownTimer::class, CountdownTimerResource::class);

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

        if ($field === self::PRODUCT_TABLE_COLUMN) {
            return $this->addProductFilter($condition);
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
     * Add filter by product
     *
     * @param int|array $product
     * @return $this
     */
    public function addProductFilter($product): Collection
    {
        $this->performAddProductFilter($product);

        return $this;
    }

    /**
     * Set filter by Countdown Timer Ids
     *
     * @param array $countdownTimerIds
     * @param bool $exclude
     * @return $this
     */
    public function addIdsFilter(array $countdownTimerIds, $exclude = false): Collection
    {
        if ($exclude) {
            $condition = ['nin' => $countdownTimerIds];
        } else {
            $condition = ['in' => $countdownTimerIds];
        }

        $this->addFieldToFilter('main_table.' . CountdownTimerInterface::COUNTDOWN_TIMER_ID, $condition);

        return $this;
    }

    /**
     * @return Collection
     */
    public function addDateFilter(): Collection
    {
        $currentDate = $this->timezone->date()->format(DateTime::DATE_PHP_FORMAT);

        $this->addFieldToFilter('main_table.' . CountdownTimerInterface::START_DATE, ['lteq' => $currentDate]);
        $this->addFieldToFilter('main_table.' . CountdownTimerInterface::END_DATE, ['gteq' => $currentDate]);

        return $this;
    }

    /**
     * @return Collection
     */
    public function addLoyalDateFilter(): Collection
    {
        $currentDate = $this->timezone->date()->format(DateTime::DATE_PHP_FORMAT);

        $whereDateFrom = $this->getConnection()->quoteInto(
            "main_table." . CountdownTimerInterface::START_DATE . " <= ?",
            $currentDate
        );
        $whereDateTo   = $this->getConnection()->quoteInto(
            "main_table." . CountdownTimerInterface::END_DATE . " >= ?",
            $currentDate
        );
        $queryExpr     = new \Zend_Db_Expr(
            "main_table." . CountdownTimerInterface::USE_DISCOUNT_DATES . " = 1" .
            " OR ( " . $whereDateFrom . " AND " . $whereDateTo . " )"
        );

        $this->getSelect()->where($queryExpr);

        return $this;
    }

    /**
     * Add all associations
     */
    public function addAllAssociations(): void
    {
        $this->addAssociatedStoreViews();
        $this->addAssociatedCustomerGroups();
        $this->addAssociatedProducts();
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
     * Perform adding filter by product
     *
     * @param int|array $product
     * @return void
     */
    protected function performAddProductFilter($product): void
    {
        if (is_array($product)) {
            $productIds = $product;
        } else {
            $productIds = [$product];
        }

        $condition = $this->getConnection()->quoteInto(
            self::PRODUCT_TABLE_ALIAS . "." . self::PRODUCT_TABLE_COLUMN . " IN(?)",
            $productIds
        );

        $condition .= " OR " . $this->getConnection()->quoteInto(
                CountdownTimerInterface::DISPLAY_MODE . " = ?",
                DisplayModeOptions::ALL_PRODUCTS
            );

        if (count($productIds) === 1) {
            /*
             * delete, when will be fully working with the products list
             * \MageWorx\CountdownTimersBase\Controller\Ajax\ProductList\GetCountdownTimersData
             *
             * currently used on product page
             */
            $condition .= " OR ( " .
                $this->getConnection()->quoteInto(
                    CountdownTimerInterface::DISPLAY_MODE . " = ?",
                    DisplayModeOptions::SPECIFIC_PRODUCTS
                ) . " AND " .
                $this->getConnection()->quoteInto(
                    CountdownTimerInterface::PRODUCTS_ASSIGN_TYPE . " = ?",
                    ProductsAssignTypeOptions::BY_CONDITIONS
                ) . " )";
        }

        $this->addFilter(self::PRODUCT_FIELD, $condition, 'string');
    }

    /**
     * Perform operations before rendering filters
     *
     * @return void
     */
    protected function _renderFiltersBefore(): void
    {
        $this->joinRelationTable(
            CountdownTimerResource::COUNTDOWN_TIMER_STORE_TABLE,
            self::STORE_TABLE_ALIAS,
            self::STORE_FIELD
        );
        $this->joinRelationTable(
            CountdownTimerResource::COUNTDOWN_TIMER_CUSTOMER_GROUP_TABLE,
            self::CUSTOMER_GROUP_TABLE_ALIAS,
            self::CUSTOMER_GROUP_FIELD
        );
        $this->joinProductRelationTable();

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
        $linkField = CountdownTimerInterface::COUNTDOWN_TIMER_ID
    ): void {

        if ($this->getFilter($field)) {
            $this->getSelect()
                 ->join(
                     [$tableAlias => $this->getTable($tableName)],
                     'main_table.' . $linkField . ' = ' . $tableAlias . '.' . $linkField,
                     []
                 );
        }
    }

    /**
     * Join product relation table if there is corresponding filter
     *
     * @param string $tableName
     * @param string $tableAlias
     * @param string $field
     * @param string $linkField
     * @return void
     */
    protected function joinProductRelationTable(
        $tableName = CountdownTimerResource::COUNTDOWN_TIMER_PRODUCT_TABLE,
        $tableAlias = self::PRODUCT_TABLE_ALIAS,
        $field = self::PRODUCT_FIELD,
        $linkField = CountdownTimerInterface::COUNTDOWN_TIMER_ID
    ): void {

        if ($this->getFilter($field)) {
            $this->getSelect()
                 ->joinLeft(
                     [$tableAlias => $this->getTable($tableName)],
                     'main_table.' . $linkField . ' = ' . $tableAlias . '.' . $linkField,
                     []
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
        $valueField = CountdownTimerInterface::COUNTDOWN_TIMER_ID,
        $labelField = CountdownTimerInterface::NAME,
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
    public function addAssociatedStoreViews(): void
    {
        $this->updateCollectionItemsUsingAssociatedEntities(
            CountdownTimerResource::COUNTDOWN_TIMER_STORE_TABLE,
            CountdownTimerInterface::STORE_IDS
        );
    }

    /**
     * @return void
     */
    protected function addAssociatedCustomerGroups(): void
    {
        $this->updateCollectionItemsUsingAssociatedEntities(
            CountdownTimerResource::COUNTDOWN_TIMER_CUSTOMER_GROUP_TABLE,
            CountdownTimerInterface::CUSTOMER_GROUP_IDS
        );
    }

    /**
     * @return void
     */
    protected function addAssociatedProducts(): void
    {
        $this->updateCollectionItemsUsingAssociatedEntities(
            CountdownTimerResource::COUNTDOWN_TIMER_PRODUCT_TABLE,
            CountdownTimerInterface::PRODUCT_IDS
        );
    }

    /**
     * @param string $tableName
     * @param string $fieldName
     */
    protected function updateCollectionItemsUsingAssociatedEntities($tableName, $fieldName): void
    {
        $ids = $this->getColumnValues(CountdownTimerInterface::COUNTDOWN_TIMER_ID);

        if (count($ids)) {
            $data       = [];
            $columnName = rtrim($fieldName, 's');
            $methodName = 'set' . str_replace('_', '', ucwords($fieldName, '_'));
            $connection = $this->getConnection();
            $select     = $connection->select()
                                     ->from($this->getTable($tableName))
                                     ->where(CountdownTimerInterface::COUNTDOWN_TIMER_ID . ' IN (?)', $ids);

            $result = $connection->fetchAll($select);

            if ($result) {

                foreach ($result as $row) {
                    $id                      = $row[CountdownTimerInterface::COUNTDOWN_TIMER_ID];
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
        $this->addFieldToFilter(CountdownTimerInterface::CONDITIONS_SERIALIZED, ['like' => $match]);

        return $this;
    }
}
