<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\RewardPoints\Model\ResourceModel\CustomerBalance\Report;

class Collection extends \MageWorx\RewardPoints\Model\ResourceModel\CustomerBalance\Collection
{
    /**
     * Collection after load operations like adding orders statistics
     *
     * @return $this
     */
    protected function _afterLoad()
    {
        $this->addWebsiteStatistics();

        return $this;
    }

    /**
     * @return $this
     */
    public function joinWebsiteTable()
    {
        if (!$this->hasFlag('is_website_table_joined')) {
            $this->getSelect()->join(
                ['website_table' => $this->getTable('store_website')],
                'main_table.website_id = website_table.website_id',
                [
                    'website_code' => 'website_table.code',
                    'website_name' => 'website_table.name'
                ]
            );

            $this->setFlag('is_website_table_joined');
        }

        return $this;
    }

    /**
     * @return $this
     */
    public function addWebsiteStatistics()
    {
        $this->joinWebsiteTable();

        $this->getSelect()
             ->reset(\Magento\Framework\DB\Select::COLUMNS)
             ->columns(
                 [
                     'main_table.website_id',
                     'website_name' => 'website_table.name',
                     'amount'       => $this->expressionFactory->create(
                         ['expression' => 'SUM(main_table.points)']
                     ),
                     'count'        => $this->expressionFactory->create(
                         ['expression' => 'COUNT(main_table.website_id)']
                     )
                 ]
             )
             ->group('main_table.website_id');

        return $this;
    }
}
