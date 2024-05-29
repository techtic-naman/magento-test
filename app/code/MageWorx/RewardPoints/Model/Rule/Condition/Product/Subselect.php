<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\RewardPoints\Model\Rule\Condition\Product;

/**
 * Subselect conditions for product.
 */
class Subselect extends \Magento\SalesRule\Model\Rule\Condition\Product\Subselect
{
    /**
     * Subselect constructor.
     *
     * @param \Magento\Rule\Model\Condition\Context $context
     * @param \MageWorx\RewardPoints\Model\Rule\Condition\Product $ruleConditionProduct
     * @param array $data
     */
    public function __construct(
        \Magento\Rule\Model\Condition\Context $context,
        \MageWorx\RewardPoints\Model\Rule\Condition\Product $ruleConditionProduct,
        array $data = []
    ) {
        parent::__construct($context, $ruleConditionProduct, $data);
        $this->setType(\MageWorx\RewardPoints\Model\Rule\Condition\Product\Subselect::class)->setValue(null);
    }
}