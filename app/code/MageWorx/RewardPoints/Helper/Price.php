<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\RewardPoints\Helper;

use Magento\Framework\Pricing\PriceCurrencyInterface;

/**
 * Price helper
 */
class Price extends \Magento\Framework\App\Helper\AbstractHelper
{

    /**
     * @var \MageWorx\RewardPoints\Helper\Data
     */
    protected $helperData;

    /**
     * @var \Magento\Framework\Pricing\PriceCurrencyInterface
     */
    protected $priceCurrency;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \MageWorx\RewardPoints\Model\PointCurrencyConverter
     */
    protected $pointConverter;

    /**
     * Price constructor.
     *
     * @param PriceCurrencyInterface $priceCurrency
     * @param Data $helperData
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \MageWorx\RewardPoints\Model\PointCurrencyConverter $pointConverter
     * @param \Magento\Framework\App\Helper\Context $context
     */
    public function __construct(
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        \MageWorx\RewardPoints\Helper\Data $helperData,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \MageWorx\RewardPoints\Model\PointCurrencyConverter $pointConverter,
        \Magento\Framework\App\Helper\Context $context
    ) {
        $this->helperData     = $helperData;
        $this->priceCurrency  = $priceCurrency;
        $this->storeManager   = $storeManager;
        $this->pointConverter = $pointConverter;
        parent::__construct($context);
    }

    /**
     * @param float $points
     * @return \Magento\Framework\Phrase
     */
    public function getFormattedPoints($points)
    {
        return __('Reward Points (%1)', $points);
    }

    /**
     * @param float $points
     * @param float $value
     * @param int|null $storeId
     */
    public function getFormattedPointsCurrency($points, $value = null, $storeId = null)
    {
        if ($value === null) {
            $value = $this->pointConverter->getCurrencyByPoints($points, $storeId);
        }

        $currency = $this->priceCurrency->convertAndFormat(
            $value,
            true,
            PriceCurrencyInterface::DEFAULT_PRECISION,
            $this->storeManager->getStore($storeId)
        );

        return __('%1 Reward Points (%2)', $points, $currency);
    }

    /**
     * @param double $value
     * @return double
     */
    public function roundPoints($value)
    {
        return $this->priceCurrency->round($value);
    }

    /**
     * @param double $points
     * @param null|int $storeId
     * @param string $header
     * @return mixed
     */
    public function getFormattedUpcomingPointsMessage($points, $storeId = null, $type = 'header', $strong = false)
    {
        switch ($type) {
            case 'header':
                $message = $this->helperData->getUpcomingPointsMessage($storeId);
                break;
            case 'cart':
                $message = $this->helperData->getCartUpcomingPointsMessage($storeId);
                break;
            default:
                $message = $this->helperData->getUpcomingPointsMessage($storeId);
                break;
        }

        if (strpos($message,'[p]') !== false) {

            $replacement = $this->roundPoints($points);

            if ($strong) {
                $replacement = '<strong>' . $replacement . '</strong>';
            }

            $message = str_replace('[p]', $replacement, $message);
        }

        if (strpos($message,'[c]') !== false) {

            $value = $this->pointConverter->getCurrencyByPoints($points, $storeId);

            $currency = $this->priceCurrency->convertAndFormat(
                $value,
                true,
                PriceCurrencyInterface::DEFAULT_PRECISION,
                $this->storeManager->getStore($storeId)
            );

            $message = str_replace('[c]', $currency, $message);
        }

        return $message;
    }

    /**
     * @param double $points
     * @param null|int $storeId
     * @return mixed
     */
    public function getFormattedPointBalanceMessage($points, $storeId = null)
    {
        if ($points) {

            $value = $this->pointConverter->getCurrencyByPoints($points, $storeId);

            // No message if points can't be generated to currency
            if (!$value) {
                return '';
            }

            $message = $this->helperData->getMinicartPointBalanceMessage($storeId);

            if (strpos($message,'[p]') !== false) {
                $message = str_replace('[p]', $this->roundPoints($points), $message);
            }

            if (strpos($message,'[c]') !== false) {

                $currency = $this->priceCurrency->convertAndFormat(
                    $value,
                    true,
                    PriceCurrencyInterface::DEFAULT_PRECISION,
                    $this->storeManager->getStore($storeId)
                );

                $message = str_replace('[c]', $currency, $message);
            }
        } else {
            $message = $this->helperData->getMinicartEmptyPointBalanceMessage($storeId);
        }

        return $message;
    }
}
