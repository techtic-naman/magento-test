<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\RewardPointsImportExport\Model\ResourceModel;

class Customer extends \Magento\Customer\Model\ResourceModel\Customer
{
    /**
     * @param $emailList
     * @return array
     */
    public function getIdsByEmails($emailList)
    {
        $bind = ['email_list' => $emailList];
        $connection = $this->getConnection();

        $select = $connection->select()->from(
            $this->getEntityTable(),
            ['email', $this->getEntityIdField()]
        )->where(
            'email IN(:email_list)'
        );

        return $connection->fetchAll($select, $bind);
    }

    /**
     * Get customer website id
     *
     * @param int $customerId
     * @return int
     */
    public function getWebsiteId($customerId)
    {
        $connection = $this->getConnection();
        $bind = ['entity_id' => (int)$customerId];
        $select = $connection->select()->from(
            $this->getTable('customer_entity'),
            'website_id'
        )->where(
            'entity_id = :entity_id'
        );

        return $connection->fetchOne($select, $bind);
    }
}
