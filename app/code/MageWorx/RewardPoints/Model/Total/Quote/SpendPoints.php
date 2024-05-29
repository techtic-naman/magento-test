<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\RewardPoints\Model\Total\Quote;

use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Quote\Model\Quote\Address;
use Magento\Quote\Api\Data\ShippingAssignmentInterface;
use MageWorx\RewardPoints\Model\Source\ApplyFor;

class SpendPoints extends \Magento\Quote\Model\Quote\Address\Total\AbstractTotal
{
    /**
     * @var \MageWorx\RewardPoints\Helper\Data
     */
    protected $helperData;


    /**
     * @var \MageWorx\RewardPoints\Helper\Price
     */
    protected $helperPrice;

    /**
     * @var \MageWorx\RewardPoints\Api\CustomerBalanceRepositoryInterface
     */
    protected $customerBalanceRepository;

    /**
     * @var \Magento\Framework\Pricing\PriceCurrencyInterface
     */
    protected $priceCurrency;

    /**
     * @var \MageWorx\RewardPoints\Model\PointCurrencyConverter;
     */
    protected $pointCurrencyConverter;

    /**
     * @todo change and test
     * @var string
     */
    protected $_code = 'reward';

    /**
     * SpendPoints constructor.
     *
     * @param \MageWorx\RewardPoints\Helper\Data $helperData
     * @param \MageWorx\RewardPoints\Helper\Price $helperPrice
     * @param \MageWorx\RewardPoints\Api\CustomerBalanceRepositoryInterface $customerBalanceRepository
     * @param PriceCurrencyInterface $priceCurrency
     * @param \MageWorx\RewardPoints\Model\PointCurrencyConverter $pointCurrencyConverter
     */
    public function __construct(
        \MageWorx\RewardPoints\Helper\Data $helperData,
        \MageWorx\RewardPoints\Helper\Price $helperPrice,
        \MageWorx\RewardPoints\Api\CustomerBalanceRepositoryInterface $customerBalanceRepository,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        \MageWorx\RewardPoints\Model\PointCurrencyConverter $pointCurrencyConverter
    ) {
        $this->helperData                = $helperData;
        $this->helperPrice               = $helperPrice;
        $this->customerBalanceRepository = $customerBalanceRepository;
        $this->priceCurrency             = $priceCurrency;
        $this->pointCurrencyConverter    = $pointCurrencyConverter;
    }

    /**
     * @param \Magento\Quote\Model\Quote $quote
     * @param ShippingAssignmentInterface $shippingAssignment
     * @param Address\Total $total
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function collect(
        \Magento\Quote\Model\Quote $quote,
        \Magento\Quote\Api\Data\ShippingAssignmentInterface $shippingAssignment,
        \Magento\Quote\Model\Quote\Address\Total $total
    ) {
        $websiteId = $quote->getStore()->getWebsiteId();
        $storeId   = $quote->getStore()->getId();

        if (!$this->helperData->isEnable($storeId)) {
            return $this;
        }

        $this->reset($total);

        if (!$quote->getUseMwRewardPoints()) {
            return $this;
        }

        if ($total->getBaseGrandTotal() >= 0 && $quote->getCustomer()->getId()) {

            /* @var $customerBalance \MageWorx\RewardPoints\Model\CustomerBalance */
            $customerBalance = $quote->getRewardPointsCustomerBalance();

            if (!$customerBalance || !$customerBalance->getId()) {
                $customerBalance = $this->customerBalanceRepository->getByCustomer(
                    $quote->getCustomer()->getId(),
                    $websiteId
                );
            }

            $customerRequestedPoints = (float)$quote->getMwRequestedPoints();

            if ($customerRequestedPoints) {
                $requestedPoints = min($customerRequestedPoints, $customerBalance->getPoints());
            } else {
                $requestedPoints = $customerBalance->getPoints();
            }

            if ($requestedPoints < 0.01) {
                throw new \Magento\Framework\Exception\LocalizedException(__('Reward Points amount is too small'));
            }

            $pointsRemainingBalance = $requestedPoints - $quote->getMwRwrdpointsAmnt();

            $currencyAmount = $this->pointCurrencyConverter->getCurrencyByPoints(
                $requestedPoints
            ); //3.5335 (EUR)

            // Case for small reward
            if (!$currencyAmount) {
                throw new \Magento\Framework\Exception\LocalizedException(__('Reward Points currency amount is zero'));
            }

            $rewardCurrencyAmountLeft = min(
                $this->priceCurrency->convert(
                    $currencyAmount,
                    $quote->getStore()
                ) - $quote->getMwRwrdpointsCurAmnt(),
                $this->getMaxPossibleCurrencyAmount($total)
            ); //3.5335 (EUR)

            $baseRewardCurrencyAmountLeft = min(
                $this->getMaxPossibleBaseCurrencyAmount($total),
                $currencyAmount - $quote->getBaseMwRwrdpointsCurAmnt()
            ); //5

            if ($baseRewardCurrencyAmountLeft >= $total->getBaseGrandTotal()) {
                $pointsBalanceUsed            = $this->pointCurrencyConverter->getPointsByCurrency(
                    $baseRewardCurrencyAmountLeft
                );
                $pointsCurrencyAmountUsed     = $total->getGrandTotal();
                $basePointsCurrencyAmountUsed = $total->getBaseGrandTotal();

                $total->setGrandTotal(0);
                $total->setBaseGrandTotal(0);
            } else {
                $pointsBalanceUsed = $this->pointCurrencyConverter->getPointsByCurrency($baseRewardCurrencyAmountLeft);
                if ($pointsBalanceUsed > $pointsRemainingBalance) {
                    $pointsBalanceUsed = $pointsRemainingBalance;
                }
                $pointsCurrencyAmountUsed     = $rewardCurrencyAmountLeft;
                $basePointsCurrencyAmountUsed = $baseRewardCurrencyAmountLeft;

                $total->setGrandTotal($total->getGrandTotal() - $pointsCurrencyAmountUsed);
                $total->setBaseGrandTotal($total->getBaseGrandTotal() - $basePointsCurrencyAmountUsed);
            }

            $quote->setMwRwrdpointsAmnt($quote->getMwRwrdpointsAmnt() + $pointsBalanceUsed);
            $quote->setMwRwrdpointsCurAmnt(
                $quote->getMwRwrdpointsCurAmnt() + $pointsCurrencyAmountUsed
            );
            $quote->setBaseMwRwrdpointsCurAmnt(
                $quote->getBaseMwRwrdpointsCurAmnt() + $basePointsCurrencyAmountUsed
            );

            $total->setMwRwrdpointsAmnt($pointsBalanceUsed);
            $total->setMwRwrdpointsCurAmnt($pointsCurrencyAmountUsed);
            $total->setBaseMwRwrdpointsCurAmnt($basePointsCurrencyAmountUsed);
        }

        return $this;
    }

    /**
     * @param string $code
     * @param \Magento\Quote\Model\Quote\Address\Total $total
     * @return float|int
     */
    public function getBaseCustomCurrencyAmountByCode($code, $total)
    {
        return 0;
    }

    /**
     * @param string $code
     * @param \Magento\Quote\Model\Quote\Address\Total $total
     * @return float|int
     */
    public function getCustomCurrencyAmountByCode($code, $total)
    {
        return 0;
    }

    /**
     * @param \Magento\Quote\Model\Quote\Address\Total $total
     * @return double
     */
    protected function getMaxPossibleBaseCurrencyAmount($total)
    {
        $baseSubtotal   = floatval($total->getBaseSubtotalWithDiscount());
        $baseShipping   = floatval($total->getBaseShippingAmount()); // - $address->getBaseShippingTaxAmount()
        $baseTax        = floatval($total->getBaseTaxAmount() + $total->getBaseDiscountTaxCompensationAmount());
        $baseGrandTotal = \floatval($total->getBaseGrandTotal());

        $basePossibleCurrencyAmount = 0;

        foreach ($this->helperData->getApplyForList() as $field) {
            switch ($field) {
                case ApplyFor::APPLY_FOR_VALUE_SUBTOTAL:
                    $basePossibleCurrencyAmount += $baseSubtotal;
                    break;
                case ApplyFor::APPLY_FOR_VALUE_SHIPPING:
                    $basePossibleCurrencyAmount += $baseShipping;
                    break;
                case ApplyFor::APPLY_FOR_VALUE_TAX:
                    $basePossibleCurrencyAmount += $baseTax;
                    break;
                default:
                    $basePossibleCurrencyAmount += $this->getBaseCustomCurrencyAmountByCode($field, $total);
            }
        }

        $result = min($basePossibleCurrencyAmount, $baseGrandTotal);

        return $result;
    }

    /**
     * @param \Magento\Quote\Model\Quote\Address\Total $total
     * @return double
     */
    protected function getMaxPossibleCurrencyAmount($total)
    {
        $subtotal   = floatval($total->getSubtotalWithDiscount());
        $shipping   = floatval($total->getShippingAmount()); // - $address->getBaseShippingTaxAmount()
        $tax        = floatval($total->getTaxAmount() + $total->getDiscountTaxCompensationAmount());
        $grandTotal = floatval($total->getGrandTotal());

        $possibleCurrencyAmount = 0;

        foreach ($this->helperData->getApplyForList() as $field) {
            switch ($field) {
                case ApplyFor::APPLY_FOR_VALUE_SUBTOTAL:
                    $possibleCurrencyAmount += $subtotal;
                    break;
                case ApplyFor::APPLY_FOR_VALUE_SHIPPING:
                    $possibleCurrencyAmount += $shipping;
                    break;
                case ApplyFor::APPLY_FOR_VALUE_TAX:
                    $possibleCurrencyAmount += $tax;
                    break;
                default:
                    $possibleCurrencyAmount += $this->getCustomCurrencyAmountByCode($field, $total);
            }
        }

        $result = min($possibleCurrencyAmount, $grandTotal);

        return $result;
    }

    /**
     * @param \Magento\Quote\Model\Quote $quote
     * @param \Magento\Quote\Model\Quote\Address\Total $total
     * @return array|void
     */
    public function fetch(\Magento\Quote\Model\Quote $quote, \Magento\Quote\Model\Quote\Address\Total $total)
    {
        if ($this->helperData->isEnable() && $total->getMwRwrdpointsCurAmnt()) {
            return [
                'code'  => $this->getCode(),
                'title' => $this->helperPrice->getFormattedPoints($total->getMwRwrdpointsAmnt()),
                'value' => -1 * $total->getMwRwrdpointsCurAmnt(),
            ];
        }

        return null;
    }

    /**
     * @param \Magento\Quote\Model\Quote\Address\Total $total
     * @return $this
     */
    protected function reset($total)
    {
        $total->setMwRwrdpointsAmnt(0)
              ->setMwRwrdpointsCurAmnt(0)
              ->setBaseMwRwrdpointsCurAmnt(0);

        return $this;
    }
}
