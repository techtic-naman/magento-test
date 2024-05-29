<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\PersonalPromotion\Model;

use Magento\Framework\DB\Select;
use Magento\Sales\Model\ResourceModel\Order as OrderResourceModel;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory as OrderCollectionFactory;
use Magento\Store\Model\StoreManagerInterface;

class PurchasesDataProvider
{
    /**
     * @var OrderResourceModel
     */
    protected $orderResourceModel;

    /**
     * @var OrderCollectionFactory
     */
    protected $orderCollectionFactory;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var array
     */
    protected $purchasedAmountByCustomerId = [];

    /**
     * @var array
     */
    protected $purchasedSkuByCustomerId = [];

    /**
     * PurchasesDataProvider constructor.
     *
     * @param OrderResourceModel $orderResourceModel
     * @param OrderCollectionFactory $orderCollectionFactory
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        OrderResourceModel $orderResourceModel,
        OrderCollectionFactory $orderCollectionFactory,
        StoreManagerInterface $storeManager
    ) {
        $this->orderResourceModel     = $orderResourceModel;
        $this->orderCollectionFactory = $orderCollectionFactory;
        $this->storeManager           = $storeManager;
    }

    /**
     * @param int $customerId
     * @return mixed
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getPurchasedAmountByCustomerId($customerId)
    {
        if (!isset($this->purchasedAmountByCustomerId[$customerId])) {
            $connection = $this->orderResourceModel->getConnection();
            $select     = $connection->select();
            $select
                ->from(
                    $this->orderResourceModel->getMainTable(),
                    [
                        'total_invoiced' => new \Zend_Db_Expr('SUM(base_total_invoiced)'),
                        'total_refunded' => new \Zend_Db_Expr('SUM(base_total_refunded)')
                    ]
                )
                ->where('customer_id = ?', $customerId)
                ->where('store_id = ?', $this->storeManager->getStore()->getId());

            $result = $connection->fetchRow($select);

            $this->purchasedAmountByCustomerId[$customerId] = $result['total_invoiced'] - $result['total_refunded'];
        }

        return $this->purchasedAmountByCustomerId[$customerId];
    }

    /**
     * @param int $customerId
     * @return mixed
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getPurchasedSkuByCustomerId($customerId)
    {
        if (!isset($this->purchasedSkuByCustomerId[$customerId])) {
            $this->purchasedSkuByCustomerId[$customerId] = [];

            $collection = $this->orderCollectionFactory->create($customerId);
            $collection->addFieldToFilter('main_table.store_id', $this->storeManager->getStore()->getId());

            $select = $collection->getSelect();
            $select->reset(Select::COLUMNS);

            $collection
                ->join(
                    ['s_o_i' => $collection->getTable('sales_order_item')],
                    'main_table.entity_id = s_o_i.order_id',
                    ['sku']
                );

            $select
                ->where(
                    new \Zend_Db_Expr('(s_o_i.qty_invoiced - s_o_i.qty_canceled - s_o_i.qty_refunded) > 0')
                )
                ->group('s_o_i.sku');

            $data = $collection->getData();

            if (!empty($data)) {
                foreach ($data as $datum) {
                    $this->purchasedSkuByCustomerId[$customerId][] = $datum['sku'];
                }
            }
        }

        return $this->purchasedSkuByCustomerId[$customerId];
    }
}
