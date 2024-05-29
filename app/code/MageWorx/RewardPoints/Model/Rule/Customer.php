<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\RewardPoints\Model\Rule;

/**
 * Reward Rule Customer Model
 *
 * @method int getRuleId()
 * @method \MageWorx\RewardPoints\Model\Rule\Customer setRuleId(int $value)
 * @method int getCustomerId()
 * @method \MageWorx\RewardPoints\Model\Rule\Customer setCustomerId(int $value)
 * @method int getTimesUsed()
 * @method \MageWorx\RewardPoints\Model\Rule\Customer setTimesUsed(int $value)
 *
 * @property \MageWorx\RewardPoints\Model\ResourceModel\Rule\Customer $_resource
 */
class Customer extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init(\MageWorx\RewardPoints\Model\ResourceModel\Rule\Customer::class);
    }

    /**
     * Load by customer rule
     *
     * @param int $customerId
     * @param int $ruleId
     * @return $this
     */
    public function loadByCustomerRule($customerId, $ruleId)
    {
        $this->_resource->loadByCustomerRule($this, $customerId, $ruleId);

        return $this;
    }
}
