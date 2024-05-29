<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\RewardPoints\Model\ResourceModel\Rule\Customer;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * Collection constructor
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init(
            \MageWorx\RewardPoints\Model\Rule\Customer::class,
            \MageWorx\RewardPoints\Model\ResourceModel\Rule\Customer::class
        );
    }
}
