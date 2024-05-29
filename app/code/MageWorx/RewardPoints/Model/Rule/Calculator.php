<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\RewardPoints\Model\Rule;

use MageWorx\RewardPoints\Model\Rule;

class Calculator implements CalculatorInterface
{
    /**
     * @var \MageWorx\RewardPoints\Helper\Price
     */
    protected $helperPrice;

    /**
     * @var CalculationStrategyFactory
     */
    protected $calculationStrategyFactory;

    /**
     * Calculator constructor.
     *
     * @param \MageWorx\RewardPoints\Helper\Price $helperPrice
     * @param CalculationStrategyFactory $calculationStrategyFactory
     */
    public function __construct(
        \MageWorx\RewardPoints\Helper\Price $helperPrice,
        \MageWorx\RewardPoints\Model\Rule\CalculationStrategyFactory $calculationStrategyFactory
    ) {
        $this->helperPrice                = $helperPrice;
        $this->calculationStrategyFactory = $calculationStrategyFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function calculatePoints($items, $rule, $address, $ignoreRuleAction = false)
    {
        if (!$rule || !$items) {
            return null;
        }

        $calculationStrategyIdentifier = $this->getCalculationStrategyIdentifier($rule);
        $calculation                   = $this->calculationStrategyFactory->create($calculationStrategyIdentifier);
        $result                        = $calculation->calculate($items, $rule, $address, $ignoreRuleAction);

        return $this->helperPrice->roundPoints($result);
    }

    /**
     * @param \MageWorx\RewardPoints\Model\Rule $rule
     * @return string
     */
    protected function getCalculationStrategyIdentifier($rule)
    {
        return $rule->getCalculationType() . '_' . $rule->getSimpleAction();
    }
}
