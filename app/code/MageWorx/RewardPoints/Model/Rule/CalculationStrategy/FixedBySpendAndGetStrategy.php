<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\RewardPoints\Model\Rule\CalculationStrategy;

class FixedBySpendAndGetStrategy extends \MageWorx\RewardPoints\Model\Rule\CalculationStrategy
{
    /**
     * {@inheritdoc}
     */
    public function calculate($items, $rule, $address, $ignoreRuleAction)
    {
        $price = $this->getPriceForCalculation($items, $rule, $address, $ignoreRuleAction);
        $pointAmount = $rule->getPointsAmount();
        $priceStep   = $rule->getPointsStep();
        $pointStage  = $rule->getPointStage();

        if (!$priceStep || !$price) {
            return 0;
        }

        $qtyForGeneration = floor(($price - $pointStage) / $priceStep);
        return $pointAmount * $qtyForGeneration;
    }
}
