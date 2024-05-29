<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\RewardPoints\Model\Rule;

use Magento\ConfigurableProduct\Model\Product\Type\Configurable;

abstract class CalculationStrategy implements \MageWorx\RewardPoints\Model\Rule\CalculationStrategyInterface
{
    /**
     * @param \Magento\Quote\Model\Quote\Item\AbstractItem[] $items
     * @param \MageWorx\RewardPoints\Model\Rule $rule
     * @param \Magento\Quote\Model\Quote\Address $address
     * @param bool $ignoreRuleAction
     * @return int
     */
    protected function getPriceForCalculation($items, $rule, $address, $ignoreRuleAction)
    {
        if ($rule->hasActionEmptyConditions() || $ignoreRuleAction) {
            return $address->getBaseSubtotalWithDiscount();
        }

        $itemsPrice = 0;

        foreach ($items as $item) {

            $parentItem = $item->getParentItem();

            if ($parentItem && $parentItem->getProductType() == Configurable::TYPE_CODE) {
                $itemsPrice += $parentItem->getBaseRowTotal() - $parentItem->getBaseDiscountAmount();
                continue;
            }
            $itemsPrice += $item->getBaseRowTotal() - $item->getBaseDiscountAmount();
        }

        return $itemsPrice;
    }

    /**
     * @param \Magento\Quote\Model\Quote\Item\AbstractItem[] $items
     * @return int
     */
    protected function getItemsQty($items)
    {
        $qty = 0;

        foreach ($items as $item) {
            $qty += $this->getItemQty($item);
        }

        return $qty;
    }

    /**
     * Return item qty
     *
     * @param \Magento\Quote\Model\Quote\Item\AbstractItem $item
     * @param \Magento\SalesRule\Model\Rule $rule
     * @return int
     */
    protected function getItemQty($item, $rule = null)
    {
        //For quote item
        $qty = $item->getTotalQty();

        //For order item
        if ($qty === null) {
            $qty = $item->getQtyOrdered();
        }

        return $qty;
    }
}
