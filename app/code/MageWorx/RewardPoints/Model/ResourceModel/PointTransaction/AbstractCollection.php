<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\RewardPoints\Model\ResourceModel\PointTransaction;


abstract class AbstractCollection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init(
            \MageWorx\RewardPoints\Model\PointTransaction::class,
            \MageWorx\RewardPoints\Model\ResourceModel\PointTransaction::class
        );
    }

    /**
     * @return $this
     */
    protected function joinCustomerBalance()
    {
        if ($this->getFlag('customerbalance_joined')) {
            return $this;
        }
        $this->getSelect()->joinInner(
            ['customerbalance_table' => $this->getTable('mageworx_rewardpoints_customer_balance')],
            'customerbalance_table.customer_balance_id = main_table.customer_balance_id',
            ['customer_id', 'points' => 'points']
        );

        return $this->setFlag('customerbalance_joined', true);
    }

    /**
     * @return $this
     */
    public function joinWebsiteTable()
    {
        $this->getSelect()->join(
            ['website_table' => $this->getTable('store_website')],
            'main_table.website_id = website_table.website_id',
            ['website_code' => 'website_table.code']
        );

        return $this;
    }

    /**
     * @return $this
     */
    public function joinCustomerTable()
    {
        $this->getSelect()->join(
            ['customer_table' => $this->getTable('customer_entity')],
            'main_table.customer_id = customer_table.entity_id',
            ['customer_email' => 'customer_table.email']
        );

        return $this;
    }

    /**
     * @param int $customerId
     * @return $this
     */
    public function addCustomerFilter($customerId)
    {
        if ($customerId) {
            $this->joinCustomerBalance();
            $this->getSelect()->where('customerbalance_table.customer_id = ?', $customerId);
        }

        return $this;
    }

    /**
     * @param int $websiteId
     * @return $this
     */
    public function addWebsiteFilter($websiteId)
    {
        $this->getSelect()->where('main_table.website_id = ?', $websiteId);

        return $this;
    }

    /**
     * @param string $eventCode
     * @return $this
     */
    public function addEventCodeFilter($eventCode)
    {
        $this->getSelect()->where('main_table.event_code = ?', $eventCode);

        return $this;
    }

    /**
     * @return $this
     */
    public function setDefaultOrder()
    {
        $this->getSelect()->reset(\Magento\Framework\DB\Select::ORDER);

        $this->addOrder('created_at', self::SORT_ORDER_DESC)
             ->addOrder('transaction_id', self::SORT_ORDER_DESC);

        return $this;
    }
}