<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace MageWorx\RewardPoints\Model\Rule\Condition\Product;

class Found extends \Magento\SalesRule\Model\Rule\Condition\Product\Found
{
    /**
     * Found constructor.
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
        $this->setType(\MageWorx\RewardPoints\Model\Rule\Condition\Product\Found::class);
    }
}
