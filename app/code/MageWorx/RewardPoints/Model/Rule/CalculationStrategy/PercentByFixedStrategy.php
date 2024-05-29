<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\RewardPoints\Model\Rule\CalculationStrategy;

class PercentByFixedStrategy extends \MageWorx\RewardPoints\Model\Rule\CalculationStrategy
{
    /**
     * {@inheritdoc}
     */
    public function calculate($items, $rule, $address, $ignoreRuleAction)
    {
        $percent = $rule->getPointsAmount();
        $price   = $this->getPriceForCalculation($items, $rule, $address, $ignoreRuleAction);

        return $price * $percent / 100;
    }
}
