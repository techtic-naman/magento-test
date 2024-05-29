<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\RewardPoints\Model\Rule\CalculationStrategy;

class PercentByBuyAndGetStrategy extends \MageWorx\RewardPoints\Model\Rule\CalculationStrategy
{
    /**
     * {@inheritdoc}
     */
    public function calculate($items, $rule, $address, $ignoreRuleAction)
    {
        $qty      = $this->getItemsQty($items);
        $price    = $this->getPriceForCalculation($items, $rule, $address, $ignoreRuleAction);
        $percent  = $rule->getPointsAmount();
        $qtyStep  = $rule->getPointsStep();
        $qtyStage = $rule->getPointStage();

        if (!$qtyStep || !$price) {
            return 0;
        }

        $qtyForCalculation = (int)max(0, $qty - $qtyStage);
        $qtyForGeneration  = floor($qtyForCalculation / $qtyStep);

        if ($qtyForGeneration < 1) {
            return 0;
        }

        return $percent * $price / 100;
    }
}
