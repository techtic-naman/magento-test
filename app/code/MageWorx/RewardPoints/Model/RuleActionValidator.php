<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace MageWorx\RewardPoints\Model;

use MageWorx\RewardPoints\Model\ResourceModel\Rule\Collection as RuleCollection;

class RuleActionValidator
{
    /**
     * @var \MageWorx\RewardPoints\Model\Rule\CalculatorInterface
     */
    protected $calculator;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \MageWorx\RewardPoints\Model\ResourceModel\Rule\CollectionFactory
     */
    protected $ruleCollectionFactory;

    /**
     * @var \Magento\Quote\Model\Quote\ItemFactory
     */
    protected $itemFactory;

    /**
     * @var \Magento\Quote\Model\Quote\AddressFactory
     */
    protected $addressFactory;

    /**
     * @var \Magento\Catalog\Model\Rule
     */
    protected $ruleCollection;

    /**
     * @var \Magento\Directory\Model\CurrencyFactory
     */
    protected $currencyFactory;

    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Quote\Model\Quote\ItemFactory $itemFactory,
        \Magento\Quote\Model\Quote\AddressFactory $addressFactory,
        \MageWorx\RewardPoints\Model\ResourceModel\Rule\CollectionFactory $rewardPointsEarnRuleCollectionFactory,
        \MageWorx\RewardPoints\Model\Rule\CalculatorInterface $calculator,
        \Magento\Directory\Model\CurrencyFactory $currencyFactory
    ) {
        $this->ruleCollectionFactory = $rewardPointsEarnRuleCollectionFactory;
        $this->calculator            = $calculator;
        $this->storeManager          = $storeManager;
        $this->itemFactory           = $itemFactory;
        $this->addressFactory        = $addressFactory;
        $this->currencyFactory       = $currencyFactory;
    }

    /**
     * @param \Magento\Catalog\Model\Product $product
     * @return float|int
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getPointsByProduct(\Magento\Catalog\Model\Product $product): float
    {
        $points = 0;

        foreach ($this->getRuleCollection($product->getCustomerGroupId()) as $rule) {

            $item = $this->itemFactory->create();
            $item->setProduct($product);
            $validate = $rule->getActions()->validate($item);

            if ($validate) {
                $rate  = null;

                if ($rate === null) {
                    $store            = $this->storeManager->getStore();
                    $currencyCodeTo   = $store->getCurrentCurrency()->getCode();
                    $currencyCodeFrom = $store->getBaseCurrency()->getCode();
                    $rate             = $this->currencyFactory->create()->load($currencyCodeTo)->getAnyRate(
                        $currencyCodeFrom
                    );
                }

                $price = $product->getPriceInfo()->getPrice('final_price')->getAmount()->getValue() * $rate;

                $address = $this->addressFactory->create();
                $address->setBaseSubtotal($price);

                $points += $this->calculator->calculatePoints(
                    [$item],
                    $rule,
                    $address,
                    true
                );
            }
        }

        return $points;
    }

    /**
     * @param int $customerGroupId
     * @return ResourceModel\Rule\Collection
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function getRuleCollection(int $customerGroupId): RuleCollection
    {
        if (!$this->ruleCollection) {
            /** @var RuleCollection $ruleCollection */
            $ruleCollection = $this->ruleCollectionFactory->create();

            $ruleCollection
                ->setValidationFilter(
                    $this->storeManager->getStore()->getWebsiteId(),
                    $customerGroupId
                )
                ->addEventFilter(\MageWorx\RewardPoints\Model\Rule::ORDER_PLACED_EARN_EVENT)
                ->addIsActiveFilter()
                ->addSortOrder();

            $this->ruleCollection = $ruleCollection;
        }

        return $this->ruleCollection;
    }
}
