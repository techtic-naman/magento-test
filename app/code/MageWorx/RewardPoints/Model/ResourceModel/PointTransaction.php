<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\RewardPoints\Model\ResourceModel;

use MageWorx\RewardPoints\Model\PointTransaction as PointTransactionModel;

class PointTransaction extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init('mageworx_rewardpoints_transaction', 'transaction_id');
        $this->_serializableFields = ['event_data' => [[], []]];
    }

    /**
     * @param string $eventCode
     * @param int $customerId
     * @param int $websiteId
     * @param int|null $entityId
     * @param int|null $ruleId
     * @return array
     */
    public function isExistTransaction($eventCode, $customerId, $websiteId, $entityId = null, $ruleId = null)
    {
        $select = $this->getConnection()
                       ->select()
                       ->from(
                           ['transaction_table' => $this->getTable('mageworx_rewardpoints_transaction')],
                           []
                       )
                       ->where('transaction_table.event_code = :event_code')
                       ->where('transaction_table.customer_id = :customer_id')
                       ->where('transaction_table.website_id = :website_id')
                       ->columns(['transaction_table.transaction_id']);

        $bind = [
            'event_code'  => $eventCode,
            'customer_id' => $customerId,
            'website_id'  => $websiteId,
        ];

        if ($entityId) {
            $select->where('transaction_table.entity_id = :entity_id');
            $bind['entity_id'] = $entityId;
        }

        if ($ruleId) {
            $select->where('transaction_table.entity_id = :rule_id');
            $bind['rule_id'] = $ruleId;
        }

        return $this->getConnection()->fetchRow($select, $bind);
    }

    /**
     * @param PointTransactionModel $object
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function addNotificationSentFlag(PointTransactionModel $object)
    {
        if ((int)$object->getId()) {
            $this->getConnection()->update(
                $this->getMainTable(),
                ['is_notification_sent' => 1],
                [$this->getIdFieldName() . '=?' => (int)$object->getId()]
            );
        }

        return $this;
    }
}
