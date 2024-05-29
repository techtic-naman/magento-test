<?php
/**
 * Copyright © MageWorx
 * See LICENSE.txt for license details.
 */
declare(strict_types = 1);

namespace MageWorx\RewardPoints\Model\Klarna\OrderLines\RewardPoints;

class Calculator
{
    protected int    $unitPrice;
    protected int    $taxRate;
    protected int    $totalAmount;
    protected int    $taxAmount;
    protected string $title;
    protected string $reference;

    public function __construct()
    {
        $this->reset();
    }

    /**
     * Calculating the values
     *
     * @param \Klarna\Orderlines\Model\Container\DataHolder $dataHolder
     */
    public function calculate($dataHolder): void
    {
        $this->reset();

        $totals = $dataHolder->getTotals();
        $total  = $totals['mageworx_rewardpoints_spend'];
        $value  = $this->toApiFloat($total->getValue());

        $this->reset()
             ->setUnitPrice($value)
             ->setTotalAmount($value)
             ->setTitle($total->getTitle()->getText())
             ->setReference($total->getCode());
    }

    /**
     * Setting the unit price
     *
     * @param int $unitPrice
     * @return self
     */
    public function setUnitPrice(int $unitPrice): self
    {
        $this->unitPrice = $unitPrice;
        return $this;
    }

    /**
     * Getting back the unit price
     *
     * @return int
     */
    public function getUnitPrice(): int
    {
        return $this->unitPrice;
    }

    /**
     * Setting the tax rate
     *
     * @param int $taxRate
     * @return self
     */
    public function setTaxRate(int $taxRate): self
    {
        $this->taxRate = $taxRate;
        return $this;
    }

    /**
     * Getting back the tax rate
     *
     * @return int
     */
    public function getTaxRate(): int
    {
        return $this->taxRate;
    }

    /**
     * Setting the total amount
     *
     * @param int $totalAmount
     * @return self
     */
    public function setTotalAmount(int $totalAmount): self
    {
        $this->totalAmount = $totalAmount;
        return $this;
    }

    /**
     * Getting back the total amount
     *
     * @return int
     */
    public function getTotalAmount(): int
    {
        return $this->totalAmount;
    }

    /**
     * Setting the tax amount
     *
     * @param int $taxAmount
     * @return self
     */
    public function setTaxAmount(int $taxAmount): self
    {
        $this->taxAmount = $taxAmount;
        return $this;
    }

    /**
     * Getting back the tax amount
     *
     * @return int
     */
    public function getTaxAmount(): int
    {
        return $this->taxAmount;
    }

    /**
     * Setting the title
     *
     * @param string $title
     * @return self
     */
    public function setTitle(string $title): self
    {
        $this->title = $title;
        return $this;
    }

    /**
     * Getting back the title
     *
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * Setting the reference
     *
     * @param string $reference
     * @return self
     */
    public function setReference(string $reference): self
    {
        $this->reference = $reference;
        return $this;
    }

    /**
     * Getting back the reference
     *
     * @return string
     */
    public function getReference(): string
    {
        return $this->reference;
    }

    /**
     * Resetting the values
     */
    protected function reset(): self
    {
        $this->setUnitPrice(0)
             ->setTaxRate(0)
             ->setTotalAmount(0)
             ->setTaxAmount(0)
             ->setTitle('')
             ->setReference('');

        return $this;
    }

    /**
     * @param $value
     * @return int
     */
    public function toApiFloat($value): int
    {
        return (int)round($value * 100);
    }
}
