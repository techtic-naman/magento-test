<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\RewardPoints\Model\Rule\CalculationStrategy;

class PercentBySpendAndGetStrategy extends \MageWorx\RewardPoints\Model\Rule\CalculationStrategy
{
    /**
     * {@inheritdoc}
     */
    public function calculate($items, $rule, $address, $ignoreRuleAction)
    {
        $percent     = $rule->getPointsAmount();
        $priceStep   = $rule->getPointsStep();
        $priceStage  = $rule->getPointStage();

        $price = $this->getPriceForCalculation($items, $rule, $address, $ignoreRuleAction);

        if (!$priceStep || !$price) {
            return 0;
        }

        $qtyForGeneration = floor($price - $priceStage / $priceStep);

        if ($qtyForGeneration < 1) {
            return 0;
        }

        return $percent * $price / 100;
    }
}
