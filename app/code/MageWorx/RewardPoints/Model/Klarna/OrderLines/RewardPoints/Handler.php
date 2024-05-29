<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types = 1);

namespace MageWorx\RewardPoints\Model\Klarna\OrderLines\RewardPoints;

use Magento\Quote\Api\Data\CartInterface;
use Magento\Sales\Api\Data\OrderInterface;

class Handler
{
    private Validator $validator;
    private Processor $processor;

    /**
     * @param Validator $validator
     * @param Processor $processor
     * @codeCoverageIgnore
     */
    public function __construct(
        Validator $validator,
        Processor $processor
    ) {
        $this->validator = $validator;
        $this->processor = $processor;
    }

    /**
     * @param \Klarna\Orderlines\Model\Container\Parameter $parameter
     * @return $this
     */
    public function fetch($parameter): Handler
    {
        if ($this->validator->isFetchable($parameter)) {
            $item = $this->getItem(
                'discount',
                'reward',
                'Reward Points',
                1,
                $parameter->getRewardTotalAmount(),
                0,
                $parameter->getRewardTotalAmount(),
                0
            );
            $parameter->addOrderLine($item);
        }

        return $this;
    }

    /**
     * @param \Klarna\Orderlines\Model\Container\Parameter $parameter
     * @param \Klarna\Orderlines\Model\Container\DataHolder $dataHolder
     * @param OrderInterface $order
     * @return $this
     */
    public function collectPostPurchase($parameter, $dataHolder, OrderInterface $order): Handler
    {
        return $this->collect($parameter, $dataHolder);
    }

    /**
     * @param \Klarna\Orderlines\Model\Container\Parameter $parameter
     * @param \Klarna\Orderlines\Model\Container\DataHolder $dataHolder
     * @param CartInterface $quote
     * @return $this
     */
    public function collectPrePurchase($parameter, $dataHolder, CartInterface $quote): Handler
    {
        return $this->collect($parameter, $dataHolder);
    }

    /**
     * Collecting the values
     *
     * @param \Klarna\Orderlines\Model\Container\Parameter $parameter
     * @param \Klarna\Orderlines\Model\Container\DataHolder $dataHolder
     * @return $this
     */
    private function collect($parameter, $dataHolder): Handler
    {
        if (!$this->validator->isCollectable($dataHolder)) {
            return $this;
        }

        $this->processor->process($dataHolder, $parameter);

        return $this;
    }

    /**
     * @param string $type
     * @param string $reference
     * @param string $name
     * @param int $quantity
     * @param float $unitPrice
     * @param float $taxRate
     * @param float $totalAmount
     * @param float $totalTaxAmount
     * @return array
     */
    private function getItem(
        string $type,
        string $reference,
        string $name,
        int    $quantity,
        float  $unitPrice,
        float  $taxRate,
        float  $totalAmount,
        float  $totalTaxAmount
    ): array {
        return [
            'type'             => $type,
            'reference'        => $reference,
            'name'             => $name,
            'quantity'         => $quantity,
            'unit_price'       => $unitPrice,
            'tax_rate'         => $taxRate,
            'total_amount'     => $totalAmount,
            'total_tax_amount' => $totalTaxAmount
        ];
    }
}
