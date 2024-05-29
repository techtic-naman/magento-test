<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\RewardPoints\Model\Rule;

interface CalculatorInterface
{
    /**
     * @param \Magento\Quote\Model\Quote\Item\AbstractItem[] $items
     * @param \MageWorx\RewardPoints\Api\Data\RuleInterface $rule
     * @param \Magento\Quote\Model\Quote\Address $address
     * @return double
     */
    public function calculatePoints($items, $rule, $address, $ignoreRuleAction = false);
}
