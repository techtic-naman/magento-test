<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\ReviewReminderBase\Model\ResourceModel;

use Exception;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Context;

class LogRecord extends AbstractDb
{
    /**
     * Event Manager
     *
     * @var ManagerInterface
     */
    protected $eventManager;

    /**
     * constructor
     *
     * @param Context $context
     * @param ManagerInterface $eventManager
     * @param mixed $connectionName
     */
    public function __construct(
        Context $context,
        ManagerInterface $eventManager,
        $connectionName = null
    ) {
        $this->eventManager = $eventManager;
        parent::__construct($context, $connectionName);
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('mageworx_reviewreminderbase_log', 'record_id');
    }

    /**
     * Retrieves Customer Email from DB by passed id.
     *
     * @param string $id
     * @return string|bool
     * @throws LocalizedException
     */
    public function getCustomerEmailById($id)
    {
        $adapter = $this->getConnection();
        $select  = $adapter->select()
                           ->from($this->getMainTable())
                           ->where('record_id = :record_id');
        $binds   = ['record_id' => (int)$id];

        return $adapter->fetchOne($select, $binds);
    }

    /**
     * @param int $numOfDays
     * @return int
     * @throws LocalizedException
     */
    public function clean($numOfDays = 60)
    {
        $where = "sent_at < '" . date('c', time() - ($numOfDays * (3600 * 24))) . "'";

        return $this->getConnection()->delete($this->getMainTable(), $where);
    }

    /**
     * @param AbstractModel $object
     * @param array $attribute
     * @return $this
     * @throws Exception
     */
    public function saveAttribute(AbstractModel $object, $attribute): LogRecord
    {
        if (is_string($attribute)) {
            $attributes = [$attribute];
        } else {
            $attributes = $attribute;
        }
        if (is_array($attributes) && !empty($attributes)) {
            $this->getConnection()->beginTransaction();
            $data = array_intersect_key($object->getData(), array_flip($attributes));
            try {
                $this->beforeSaveAttribute($object, $attributes);
                if ($object->getId() && !empty($data)) {
                    $this->getConnection()->update(
                        $this->getMainTable(),
                        $data,
                        [$this->getIdFieldName() . '= ?' => (int)$object->getId()]
                    );
                    $object->addData($data);
                }
                $this->afterSaveAttribute($object, $attributes);
                $this->getConnection()->commit();
            } catch (Exception $e) {
                $this->getConnection()->rollBack();
                throw $e;
            }
        }

        return $this;
    }

    /**
     * @param AbstractModel $object
     * @param array $attribute
     * @return $this
     */
    protected function beforeSaveAttribute(AbstractModel $object, $attribute): LogRecord
    {
        if ($object->getEventObject() && $object->getEventPrefix()) {
            $this->eventManager->dispatch(
                $object->getEventPrefix() . '_save_attribute_before',
                [
                    $object->getEventObject() => $this,
                    'object'                  => $object,
                    'attribute'               => $attribute
                ]
            );
        }

        return $this;
    }

    /**
     * After save object attribute
     *
     * @param AbstractModel $object
     * @param string $attribute
     * @return $this
     */
    protected function afterSaveAttribute(AbstractModel $object, $attribute): LogRecord
    {
        if ($object->getEventObject() && $object->getEventPrefix()) {
            $this->eventManager->dispatch(
                $object->getEventPrefix() . '_save_attribute_after',
                [
                    $object->getEventObject() => $this,
                    'object'                  => $object,
                    'attribute'               => $attribute
                ]
            );
        }

        return $this;
    }
}
