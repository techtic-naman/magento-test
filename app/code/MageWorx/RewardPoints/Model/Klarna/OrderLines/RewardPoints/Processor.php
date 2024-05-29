<?php
/**
 * Copyright © MageWorx
 * See LICENSE.txt for license details.
 */
declare(strict_types = 1);

namespace MageWorx\RewardPoints\Model\Klarna\OrderLines\RewardPoints;

class Processor
{
    /**
     * @var Calculator
     */
    private Calculator $calculator;

    /**
     * @param Calculator $calculator
     * @codeCoverageIgnore
     */
    public function __construct(Calculator $calculator)
    {
        $this->calculator = $calculator;
    }

    /**
     * Processing the data and putting the data into the Parameter instance
     *
     * @param \Klarna\Orderlines\Model\Container\DataHolder $dataHolder
     * @param \Klarna\Orderlines\Model\Container\Parameter $parameter
     */
    public function process($dataHolder, $parameter): void
    {
        $this->calculator->calculate($dataHolder);

        $parameter->setRewardUnitPrice($this->calculator->getUnitPrice())
                  ->setRewardTaxRate($this->calculator->getTaxRate())
                  ->setRewardTotalAmount($this->calculator->getTotalAmount())
                  ->setRewardTaxAmount($this->calculator->getTaxAmount())
                  ->setRewardTitle($this->calculator->getTitle())
                  ->setRewardReference($this->calculator->getReference());
    }
}
