<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\RewardPointsImportExport\Plugin;

class ExtendCustomerGrid
{
    /**
     * @param \Magento\Framework\View\Element\UiComponent\DataProvider\FilterPool $subject
     * @param \Magento\Framework\View\Element\UiComponent\DataProvider\SearchResult $collection
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return array
     * @throws \Zend_Db_Select_Exception
     */
    public function beforeApplyFilters($subject, $collection, $searchCriteria)
    {
        if ($searchCriteria->getRequestName() == 'customer_listing_data_source') {

            if ($collection->getMainTable() === $collection->getResource()->getTable('customer_grid_flat')) {

                $customerBalanceTableName = $collection->getResource()->getTable(
                    'mageworx_rewardpoints_customer_balance'
                );

                /**@var \Magento\Customer\Model\ResourceModel\Grid\Collection $collection */
                $collection
                    ->addFilterToMap('website_id', 'main_table.website_id')
                    ->addFilterToMap(
                        'points',
                        new \Zend_Db_Expr('IFNULL(`cbt`.`points`, 0)')
                    )
                    ->getSelect()
                    ->joinLeft(
                        ['cbt' => $customerBalanceTableName],
                        "cbt.customer_id = main_table.entity_id AND cbt.website_id = main_table.website_id",
                        [
                            'points' => new \Zend_Db_Expr('IFNULL(`cbt`.`points`, 0)')
                        ]
                    );

                $where = $collection->getSelect()->getPart(\Magento\Framework\DB\Select::WHERE);

                $collection->getSelect()
                    ->setPart(
                        \Magento\Framework\DB\Select::WHERE,
                        $where
                    )
                    ->group('main_table.entity_id');
            }
        }

        return [$collection, $searchCriteria];
    }
}