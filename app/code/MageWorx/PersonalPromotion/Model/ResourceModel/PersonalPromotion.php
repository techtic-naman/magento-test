<?php
/**
 * Copyright © 2018 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\PersonalPromotion\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\App\ResourceConnection as AppResource;

class PersonalPromotion extends AbstractDb
{
    /**
     * Store model
     *
     * @var null|\Magento\Store\Model\Store
     */
    protected $store = null;

    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Framework\DB\Adapter\AdapterInterface
     */
    private $connection;

    /**
     * Construct
     *
     * @param \Magento\Framework\Model\ResourceModel\Db\Context $context
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param string $connectionName
     */
    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        $connectionName = null
    ) {
        parent::__construct($context, $connectionName);
        $this->storeManager = $storeManager;
        $this->connection   = $this->getConnection();
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('mageworx_personalpromotion', 'personal_id');
    }

    /**
     * @param array $data
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function saveRuleCustomerRelations($data)
    {
        $tableName = $this->getMainTable();
        $this->connection->insertMultiple($tableName, $data);
    }

    /**
     * @param int $salesRuleId
     */
    public function deleteCustomersByRuleId($salesRuleId)
    {
        $this->connection->delete(
            $this->_resources->getTableName('mageworx_personalpromotion'),
            [
                'sales_rule_id = ?' => $salesRuleId
            ]
        );
    }

    /**
     * @param int $ruleId
     * @return array
     */
    public function getCustomerIdsByRuleId($ruleId)
    {
        $select = $this->connection->select()
                                   ->from(
                                       $this->_resources->getTableName('mageworx_personalpromotion'),
                                       'customer_id'
                                   )
                                   ->where('sales_rule_id = ?', $ruleId);

        return $this->connection->fetchCol($select);
    }

    /**
     * @param int $customerId
     * @param string $linkField
     * @return array
     */
    public function getRuleIdsByCustomerId($customerId, $linkField)
    {
        $select = $this->connection->select()
            ->from(
                ['sr' => $this->_resources->getTableName('salesrule')], $linkField
            )->join(
                ['mp' => $this->_resources->getTableName('mageworx_personalpromotion')],
                "sr." . $linkField . " = mp.sales_rule_id",
                ['']
            )

           ->where('mp.customer_id = ?', $customerId);

        return $this->connection->fetchCol($select);
    }

    /**
     * Retrieve rules which have assigned customers
     *
     * @param array $ruleIds
     * @return array
     */
    public function getCustomersRuleIds(array $ruleIds)
    {
        $select = $this->connection->select()
                                   ->distinct(true)
                                   ->from(
                                       $this->_resources->getTableName('mageworx_personalpromotion'),
                                       'sales_rule_id'
                                   )
                                   ->where('sales_rule_id IN(?)', $ruleIds);

        return $this->connection->fetchCol($select);
    }

    /**
     * @param int $ruleId
     * @param int|null $updatedId
     * @return null|string
     * @throws \Zend_Db_Select_Exception
     */
    public function getRowIdByRuleId($ruleId, $ruleFileldId, $updatedId = null)
    {
        $select = $this->connection->select();
        $select->setPart('disable_staging_preview', true);
        $select->from($this->_resources->getTableName('salesrule'), [$ruleFileldId]);
        $select->where('rule_id = ?', $ruleId);

        if (isset($updatedId)) {
            $select->where('created_in = ?', $updatedId);
        }

        $rowId = $this->connection->fetchOne($select);

        return $rowId ?: null;
    }

}
