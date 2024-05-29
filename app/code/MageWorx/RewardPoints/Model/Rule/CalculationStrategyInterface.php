<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\RewardPoints\Model\Rule;

interface CalculationStrategyInterface
{
    /**
     * @param \Magento\Quote\Model\Quote\Item\AbstractItem[] $items
     * @param \MageWorx\RewardPoints\Api\Data\RuleInterface $rule
     * @param \Magento\Quote\Model\Quote\Address $address
     * @param bool $ignoreRuleAction
     * @return double
     */
    public function calculate($items, $rule, $address, $ignoreRuleAction);
}
