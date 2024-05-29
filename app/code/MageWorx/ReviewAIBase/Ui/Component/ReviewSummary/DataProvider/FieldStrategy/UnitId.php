<?php

namespace MageWorx\ReviewAIBase\Ui\Component\ReviewSummary\DataProvider\FieldStrategy;

use Magento\Framework\Data\Collection;
use Magento\Ui\DataProvider\AddFieldToCollectionInterface;

class UnitId implements AddFieldToCollectionInterface
{
    /**
     * Add field to collection reflection
     *
     * @param Collection $collection
     * @param string $field
     * @param string|null $alias
     * @return void
     */
    public function addField(Collection $collection, $field, $alias = null)
    {
        // join table
        $alias = $alias ?? '';
        $this->_addField($collection, $field, $alias);
    }

    /**
     * Check is catalog_product_entity table already has been joined to the collection
     *
     * @param Collection $collection
     * @return bool
     */
    protected function isTableJoined(Collection $collection): bool
    {
        $select   = $collection->getSelect();
        $fromPart = $select->getPart('from');

        return !empty($fromPart['catalog_product_entity']);
    }

    /**
     * @param Collection $collection
     * @param string $field
     * @param string $alias
     * @return void
     * @throws \Zend_Db_Select_Exception
     */
    protected function _addField(Collection $collection, string $field, string $alias): void
    {
        /** @var \Zend_Db_Select $select */
//        $select = $collection->getSelect();
//        if ($this->isTableJoined($collection)) {
//            $select->columns(['unit_id' => new \Zend_Db_Expr('CONCAT(catalog_product_entity.entity_id, "_", main_table.store_id)')]);
//        } else {
//            $select->joinRight(
//                [$collection->getResource()->getTable('catalog_product_entity')],
//                'main_table.product_id = catalog_product_entity.entity_id'
//            )->columns(['unit_id' => new \Zend_Db_Expr('CONCAT(catalog_product_entity.entity_id, "_")')]);
//        }
    }
}
