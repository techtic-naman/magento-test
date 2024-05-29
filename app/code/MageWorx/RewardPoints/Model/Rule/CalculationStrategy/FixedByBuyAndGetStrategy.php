<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\RewardPoints\Model\Rule\CalculationStrategy;

class FixedByBuyAndGetStrategy extends \MageWorx\RewardPoints\Model\Rule\CalculationStrategy
{
    /**
     * {@inheritdoc}
     */
    public function calculate($items, $rule, $address, $ignoreRuleAction)
    {
        $qty = $this->getItemsQty($items);

        $pointAmount = $rule->getPointsAmount();
        $qtyStep     = $rule->getPointsStep();
        $qtyStage    = $rule->getPointStage();

        if (!$qtyStep || !$qty) {
            return 0;
        }

        $qtyForCalculation = (int)max(0, $qty - $qtyStage);

        $qtyForGeneration = floor($qtyForCalculation / $qtyStep);
        return $pointAmount * $qtyForGeneration;
    }
}
