<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types = 1);

namespace MageWorx\ReviewAIBase\Model\ResourceModel\ReviewSummary\Grid;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Framework\Data\Collection\Db\FetchStrategyInterface;
use Magento\Framework\Data\Collection\EntityFactoryInterface as EntityFactoryInterfaceAlias;
use Magento\Framework\DataObject;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Eav\Model\Config as EavConfig;
use Psr\Log\LoggerInterface;
use Magento\Framework\EntityManager\MetadataPool;

class Collection extends AbstractCollection
{
    const RS_COLUMNS = [
        'store_id',
        'status',
        'summary_data',
        'created_at',
        'updated_at',
        'update_required',
        'is_enabled'
    ];

    protected EavConfig    $eavConfig;
    protected MetadataPool $metadataPool;

    public function __construct(
        EntityFactoryInterfaceAlias $entityFactory,
        LoggerInterface             $logger,
        FetchStrategyInterface      $fetchStrategy,
        ManagerInterface            $eventManager,
        EavConfig                   $eavConfig,
        MetadataPool                $metadataPool,
        AdapterInterface            $connection = null,
        AbstractDb                  $resource = null
    ) {
        $this->eavConfig    = $eavConfig;
        $this->metadataPool = $metadataPool;
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $connection, $resource);
    }

    /**
     * Update main table to suite our grid needs: show all products
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init(
            Product::class,
            \Magento\Catalog\Model\ResourceModel\Product::class
        );
    }

    public function getMainTable()
    {
        if ($this->_mainTable === null) {
            $this->setMainTable('catalog_product_entity');
        }

        return $this->_mainTable;
    }

    public function addFieldToFilter($field, $condition = null)
    {
        if ($field === 'entity_id') {
            $field = 'main_table.entity_id';
        }

        return parent::addFieldToFilter($field, $condition);
    }

    protected function _initSelect()
    {
        parent::_initSelect();

        $rsTableJoinCond = 'main_table.entity_id = rs.product_id';
        $this->getSelect()
             ->joinLeft(
                 ['rs' => $this->getTable('mageworx_reviewai_review_summary')],
                 $rsTableJoinCond,
                 [
                     'rs.product_id as orig_product_id',
                     'rs.entity_id as rs_id',
                     'rs.store_id',
                     'rs.status',
                     'rs.summary_data',
                     'rs.created_at',
                     'rs.updated_at',
                     'rs.update_required',
                     'rs.is_enabled'
                 ]
             );

        // Detect the edition of Magento to use correct column name for the join
        $joinField = $this->metadataPool->getMetadata(ProductInterface::class)->getLinkField();

        // Get the attribute ID for visibility
        $attributeId = $this->getProductAttributeIdByCode('visibility');
        if ($attributeId) {
            $this->getSelect()->distinct(true);

            $this->getSelect()->joinLeft(
                ['visibility_table' => $this->getTable('catalog_product_entity_int')],
                "main_table.{$joinField} = visibility_table.{$joinField} AND visibility_table.attribute_id = " . $attributeId,
                []
            );

            // Filter out products not visible individually
            $this->addFieldToFilter('visibility_table.value', ['neq' => Visibility::VISIBILITY_NOT_VISIBLE]);
        }

        // Join Product Name
        $productEntityVarcharTable  = $this->getTable('catalog_product_entity_varchar');
        $productEntityVarcharAttrId = $this->getProductAttributeIdByCode('name');
        if ($productEntityVarcharAttrId) {
            $this->getSelect()->joinLeft(
                ['product_name_table' => $productEntityVarcharTable],
                "main_table.{$joinField} = product_name_table.{$joinField} AND product_name_table.attribute_id = {$productEntityVarcharAttrId}",
                ['name' => 'product_name_table.value']
            );
            $this->addFilterToMap('name', 'product_name_table.value');
        }

        foreach (static::RS_COLUMNS as $column) {
            $this->addFilterToMap($column, 'rs.' . $column);
        }

        return $this;
    }

    /**
     * @param DataObject $item
     * @return $this|Collection
     */
    public function addItem(DataObject $item): Collection
    {
        $itemId = $this->_getItemId($item);

        if ($itemId !== null) {
            $this->_items[$itemId] = $item;
        } else {
            $this->_addItem($item);
        }

        return $this;
    }

    /**
     * Get attribute ID by attribute code
     *
     * @param string $attributeCode
     * @return int
     * @throws LocalizedException
     */

    protected function getProductAttributeIdByCode(string $attributeCode): int
    {
        $entityType   = $this->eavConfig->getEntityType(Product::ENTITY);
        $entityTypeId = $entityType->getEntityTypeId();

        $connection = $this->getConnection();
        $select     = $connection->select()
                                 ->from(['eav' => $this->getTable('eav_attribute')], 'attribute_id')
                                 ->where('eav.entity_type_id = ?', $entityTypeId)
                                 ->where('eav.attribute_code = ?', $attributeCode);

        return (int)$connection->fetchOne($select);
    }

    /**
     * @param int $storeId
     * @return $this
     * @throws \Zend_Db_Select_Exception
     */
    public function addStoreFilterToJoin(int $storeId): Collection
    {
        $fromPart = $this->getSelect()->getPart('from');

        if (isset($fromPart['rs']['joinCondition'])) {
            $fromPart['rs']['joinCondition'] .= ' AND ' . $this->getConnection()->quoteInto('rs.store_id = ?', $storeId);

            $this->getSelect()->setPart('from', $fromPart);
        }

        return $this;
    }
}
