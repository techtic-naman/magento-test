<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\RewardPoints\Model\ResourceModel;

use MageWorx\RewardPoints\Model\CustomerBalance as CustomerBalanceModel;

class CustomerBalance extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init('mageworx_rewardpoints_customer_balance', 'customer_balance_id');
    }

    /**
     * @param CustomerBalanceModel $customerBalance
     * @param int $customerId
     * @param int $websiteId
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function loadByCustomer(CustomerBalanceModel $customerBalance, $customerId, $websiteId)
    {
        $select = $this->getConnection()->select();
        $select->from($this->getMainTable())
               ->where('customer_id = ?', $customerId)
               ->where('website_id = ?', $websiteId)
               ->columns($this->getIdFieldName());

        $data = $this->getConnection()->fetchRow($select);

        $id = $data[$this->getIdFieldName()] ?? 0;

        return $this->load($customerBalance, $id);
    }
}
