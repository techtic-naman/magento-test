<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\RewardPoints\Model\ResourceModel\Rule;

use Magento\Framework\Serialize\Serializer\Json;
use Magento\Quote\Model\Quote\Address;


/**
 * Reward Rules resource collection model.
 */
class Collection extends \Magento\Rule\Model\ResourceModel\Rule\Collection\AbstractCollection
{

    /**
     * @var string
     */
    protected $_idFieldName = 'rule_id';

    /**
     * Store associated with rule entities information map
     *
     * @var array
     */
    protected $_associatedEntitiesMap;

    /**
     * @var \Magento\SalesRule\Model\ResourceModel\Rule\DateApplier
     */
    protected $dateApplier;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $date;

    /**
     * @var Json $serializer
     */
    private $serializer;

    /**
     * @param \Magento\Framework\Data\Collection\EntityFactory $entityFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $date
     * @param mixed $connection
     * @param \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource
     * @param Json $serializer Optional parameter for backward compatibility
     */
    public function __construct(
        \Magento\Framework\Data\Collection\EntityFactory $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $date,
        \Magento\SalesRule\Model\ResourceModel\Rule\DateApplier $dateApplier,
        \Magento\Framework\Serialize\Serializer\Json $serializer,
        \MageWorx\RewardPoints\Model\ResourceModel\Rule\AssociatedEntityMap $associatedEntityMap,
        \Magento\Framework\DB\Adapter\AdapterInterface $connection = null,
        \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource = null
    ) {
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $connection, $resource);
        $this->date                   = $date;
        $this->dateApplier            = $dateApplier;
        $this->serializer             = $serializer;
        $this->_associatedEntitiesMap = $associatedEntityMap->getData();
    }

    /**
     * Set resource model and determine field mapping
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\MageWorx\RewardPoints\Model\Rule::class, \MageWorx\RewardPoints\Model\ResourceModel\Rule::class);
        $this->_map['fields']['rule_id'] = 'main_table.rule_id';
    }

    /**
     * @return $this
     * @throws \Exception
     */
    protected function _afterLoad()
    {
        $this->mapAssociatedEntities('website', 'website_ids');
        $this->mapAssociatedEntities('customer_group', 'customer_group_ids');

        $this->setFlag('add_websites_to_result', false);

        return parent::_afterLoad();
    }

    /**
     * @param string $entityType
     * @param string $objectField
     * @throws \Magento\Framework\Exception\LocalizedException
     * @return void
     */
    protected function mapAssociatedEntities($entityType, $objectField)
    {
        if (!$this->_items) {
            return;
        }

        $entityInfo  = $this->_getAssociatedEntityInfo($entityType);
        $ruleIdField = $entityInfo['rule_id_field'];
        $entityIds   = $this->getColumnValues($ruleIdField);

        $select = $this->getConnection()->select()
                       ->from($this->getTable($entityInfo['associations_table']))
                       ->where($ruleIdField . ' IN (?)', $entityIds);

        $associatedEntities = $this->getConnection()->fetchAll($select);

        array_map(
            function ($associatedEntity) use ($entityInfo, $ruleIdField, $objectField) {
                $item                  = $this->getItemByColumnValue($ruleIdField, $associatedEntity[$ruleIdField]);
                $itemAssociatedValue   = is_null($item->getData($objectField)) ? [] : $item->getData($objectField);
                $itemAssociatedValue[] = $associatedEntity[$entityInfo['entity_id_field']];
                $item->setData($objectField, $itemAssociatedValue);
            },
            $associatedEntities
        );
    }

    /**
     * @param int $websiteId
     * @param int $customerGroupId
     * @param string|null $now
     * @param Address|null $address
     * @return $this
     */
    public function setValidationFilter(
        $websiteId,
        $customerGroupId,
        $now = null,
        Address $address = null
    ) {
        if (!$this->getFlag('validation_filter')) {
            $this->addWebsiteGroupDateFilter($websiteId, $customerGroupId, $now);
            $this->setOrder('sort_order', self::SORT_ORDER_ASC);
            $this->setFlag('validation_filter', true);
        }

        return $this;
    }

    /**
     * Filter collection by website(s), customer group(s) and date.
     * Filter collection to only active rules.
     * Sorting is not involved
     *
     * @param int $websiteId
     * @param int $customerGroupId
     * @param string|null $now
     * @use $this->addWebsiteFilter()
     * @return $this
     */
    public function addWebsiteGroupDateFilter($websiteId, $customerGroupId, $now = null)
    {
        if (!$this->getFlag('website_group_date_filter')) {
            if ($now === null) {
                $now = $this->date->date()->format('Y-m-d');
            }

            $this->addWebsiteFilter($websiteId);

            $entityInfo = $this->_getAssociatedEntityInfo('customer_group');
            $connection = $this->getConnection();
            $this->getSelect()->joinInner(
                ['customer_group_ids' => $this->getTable($entityInfo['associations_table'])],
                $connection->quoteInto(
                    'main_table.' .
                    $entityInfo['rule_id_field'] .
                    ' = customer_group_ids.' .
                    $entityInfo['rule_id_field'] .
                    ' AND customer_group_ids.' .
                    $entityInfo['entity_id_field'] .
                    ' = ?',
                    (int)$customerGroupId
                ),
                []
            );

            $this->dateApplier->applyDate($this->getSelect(), $now);
            $this->addIsActiveFilter();
            $this->setFlag('website_group_date_filter', true);
        }

        return $this;
    }

    /**
     * @param int|int[]|\Magento\Store\Model\Website $websiteId
     * @return Collection
     */
    public function addWebsiteFilter($websiteId)
    {
        //Convert UI grid condition
        if (is_array($websiteId) && !empty($websiteId['in'])) {
            $websiteId = $websiteId['in'];
        }

        return parent::addWebsiteFilter($websiteId);
    }


    public function addEventFilter($value)
    {
        return $this->addFieldToFilter('event', $value);
    }

    /**
     * Find product attribute in conditions or actions
     *
     * @param string $attributeCode
     * @return $this
     */
    public function addAttributeInConditionFilter($attributeCode)
    {
        $match = sprintf('%%%s%%', substr($this->serializer->serialize(['attribute' => $attributeCode]), 1, -1));
        /**
         * Information about conditions and actions stored in table as JSON encoded array
         * in fields conditions_serialized and actions_serialized.
         * If you want to find rules that contains some particular attribute, the easiest way to do so is serialize
         * attribute code in the same way as it stored in the serialized columns and execute SQL search
         * with like condition.
         * Table
         * +-------------------------------------------------------------------+
         * |     conditions_serialized       |         actions_serialized      |
         * +-------------------------------------------------------------------+
         * | {..."attribute":"attr_name"...} | {..."attribute":"attr_name"...} |
         * +---------------------------------|---------------------------------+
         * From attribute code "attr_code", will be generated such SQL:
         * `condition_serialized` LIKE '%"attribute":"attr_name"%'
         *      OR `actions_serialized` LIKE '%"attribute":"attr_name"%'
         */
        $field = $this->_getMappedField('conditions_serialized');
        $cCond = $this->_getConditionSql($field, ['like' => $match]);

        $field = $this->_getMappedField('actions_serialized');
        $aCond = $this->_getConditionSql($field, ['like' => $match]);

        $this->getSelect()->where(
            sprintf('(%s OR %s)', $cCond, $aCond),
            null,
            \Magento\Framework\DB\Select::TYPE_CONDITION
        );

        return $this;
    }

    /**
     * Limit rules collection by specific customer group
     *
     * @param int $customerGroupId
     * @return $this
     */
    public function addCustomerGroupFilter($customerGroupId)
    {
        $entityInfo = $this->_getAssociatedEntityInfo('customer_group');
        if (!$this->getFlag('is_customer_group_joined')) {
            $this->setFlag('is_customer_group_joined', true);
            $this->getSelect()->join(
                ['customer_group' => $this->getTable($entityInfo['associations_table'])],
                $this->getConnection()
                     ->quoteInto('customer_group.' . $entityInfo['entity_id_field'] . ' = ?', $customerGroupId)
                . ' AND main_table.' . $entityInfo['rule_id_field'] . ' = customer_group.'
                . $entityInfo['rule_id_field'],
                []
            );
        }

        return $this;
    }

    /**
     * Add sort order by ascending
     *
     * @return $this
     */
    public function addSortOrder()
    {
        $this->setOrder('main_table.sort_order', self::SORT_ORDER_ASC);

        return $this;
    }
}
