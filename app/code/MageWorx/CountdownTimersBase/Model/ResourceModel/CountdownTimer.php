<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace MageWorx\CountdownTimersBase\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use MageWorx\CountdownTimersBase\Api\Data\CountdownTimerInterface;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\DataObject;

class CountdownTimer extends AbstractDb
{
    const COUNTDOWN_TIMER_TABLE                = 'mageworx_countdowntimersbase_countdown_timer';
    const COUNTDOWN_TIMER_STORE_TABLE          = 'mageworx_countdowntimersbase_countdown_timer_store';
    const COUNTDOWN_TIMER_CUSTOMER_GROUP_TABLE = 'mageworx_countdowntimersbase_countdown_timer_customer_group';
    const COUNTDOWN_TIMER_PRODUCT_TABLE        = 'mageworx_countdowntimersbase_countdown_timer_product';
    const COUNTDOWN_TIMER_TEMPLATE_TABLE       = 'mageworx_countdowntimersbase_countdown_timer_template';

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct(): void
    {
        $this->_init(self::COUNTDOWN_TIMER_TABLE, CountdownTimerInterface::COUNTDOWN_TIMER_ID);
    }

    /**
     * @param AbstractModel $object
     * @return AbstractDb
     */
    protected function _afterSave(AbstractModel $object): AbstractDb
    {
        $this->assignCountdownTimerToStoreViews($object);
        $this->assignCountdownTimerToCustomerGroups($object);
        $this->assignCountdownTimerToProducts($object);

        return parent::_afterSave($object);
    }

    /**
     * @param CountdownTimerInterface|AbstractModel $countdownTimer
     */
    protected function assignCountdownTimerToStoreViews($countdownTimer): void
    {
        $this->assignCountdownTimerToEntity(
            $countdownTimer,
            self::COUNTDOWN_TIMER_STORE_TABLE,
            CountdownTimerInterface::STORE_IDS
        );
    }

    /**
     * @param CountdownTimerInterface|AbstractModel $countdownTimer
     */
    protected function assignCountdownTimerToCustomerGroups($countdownTimer): void
    {
        $this->assignCountdownTimerToEntity(
            $countdownTimer,
            self::COUNTDOWN_TIMER_CUSTOMER_GROUP_TABLE,
            CountdownTimerInterface::CUSTOMER_GROUP_IDS
        );
    }

    /**
     * @param CountdownTimerInterface|AbstractModel $countdownTimer
     */
    protected function assignCountdownTimerToProducts($countdownTimer): void
    {
        $this->assignCountdownTimerToEntity(
            $countdownTimer,
            self::COUNTDOWN_TIMER_PRODUCT_TABLE,
            CountdownTimerInterface::PRODUCT_IDS
        );
    }

    /**
     * @param CountdownTimerInterface|AbstractModel $countdownTimer
     * @param string $tableName
     * @param string $fieldName
     */
    protected function assignCountdownTimerToEntity($countdownTimer, $tableName, $fieldName): void
    {
        $methodName = 'get' . str_replace('_', '', ucwords($fieldName, '_'));
        $columnName = rtrim($fieldName, 's');

        $newIds = (array)$countdownTimer->{$methodName}();
        $oldIds = $this->getOldEntityIds($countdownTimer->getId(), $tableName, $columnName);

        $table  = $this->getTable($tableName);
        $delete = array_diff($oldIds, $newIds);
        $insert = array_diff($newIds, $oldIds);

        if ($delete) {
            $where = [
                CountdownTimerInterface::COUNTDOWN_TIMER_ID . '= ?' => $countdownTimer->getId(),
                $columnName . ' IN (?)'                             => $delete
            ];
            $this->getConnection()->delete($table, $where);
        }

        if ($insert) {
            $data = [];

            foreach ($insert as $entityId) {
                $data[] = [
                    CountdownTimerInterface::COUNTDOWN_TIMER_ID => $countdownTimer->getId(),
                    $columnName                                 => $entityId
                ];
            }
            $this->getConnection()->insertMultiple($table, $data);
        }
    }

    /**
     * @param int $countdownTimerId
     * @param string $tableName
     * @param string $columnName
     * @return array
     */
    protected function getOldEntityIds($countdownTimerId, $tableName, $columnName): array
    {
        $connection = $this->getConnection();

        $select = $connection->select()->from(
            $this->getTable($tableName),
            $columnName
        )->where(
            CountdownTimerInterface::COUNTDOWN_TIMER_ID . ' = ?',
            $countdownTimerId
        );

        return $connection->fetchCol($select);
    }

    /**
     * @param AbstractModel|DataObject $object
     * @return CountdownTimer
     */
    protected function _afterLoad(AbstractModel $object): CountdownTimer
    {
        $this->addAssociatedStoreViews($object);
        $this->addAssociatedCustomerGroups($object);
        $this->addAssociatedProducts($object);

        return parent::_afterLoad($object);
    }

    /**
     * @param AbstractModel|DataObject $object
     */
    protected function addAssociatedStoreViews(AbstractModel $object): void
    {
        $this->updateObjectUsingAssociatedEntities(
            $object,
            self::COUNTDOWN_TIMER_STORE_TABLE,
            CountdownTimerInterface::STORE_IDS
        );
    }

    /**
     * @param AbstractModel|DataObject $object
     */
    protected function addAssociatedCustomerGroups(AbstractModel $object): void
    {
        $this->updateObjectUsingAssociatedEntities(
            $object,
            self::COUNTDOWN_TIMER_CUSTOMER_GROUP_TABLE,
            CountdownTimerInterface::CUSTOMER_GROUP_IDS
        );
    }

    /**
     * @param AbstractModel|DataObject $object
     */
    protected function addAssociatedProducts(AbstractModel $object): void
    {
        $this->updateObjectUsingAssociatedEntities(
            $object,
            self::COUNTDOWN_TIMER_PRODUCT_TABLE,
            CountdownTimerInterface::PRODUCT_IDS
        );
    }

    /**
     * @param AbstractModel|DataObject $object
     * @param string $tableName
     * @param string $fieldName
     */
    protected function updateObjectUsingAssociatedEntities($object, $tableName, $fieldName): void
    {
        $id = $object->getId();

        if ($id) {
            $data       = [];
            $columnName = rtrim($fieldName, 's');
            $methodName = 'set' . str_replace('_', '', ucwords($fieldName, '_'));
            $connection = $this->getConnection();
            $select     = $connection->select()
                                     ->from($this->getTable($tableName))
                                     ->where(CountdownTimerInterface::COUNTDOWN_TIMER_ID . ' = ?', $id);

            $result = $connection->fetchAll($select);

            if ($result) {

                foreach ($result as $row) {
                    $data[] = $row[$columnName];
                }
            }

            $object->{$methodName}($data);
        }
    }
}
