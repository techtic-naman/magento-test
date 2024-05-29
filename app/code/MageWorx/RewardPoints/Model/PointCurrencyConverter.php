<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\RewardPoints\Model;

class PointCurrencyConverter
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
     * PointCurrencyConverter constructor.
     *
     * @param \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency
     * @param \MageWorx\RewardPoints\Helper\Data $helperData
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\App\Helper\Context $context
     */
    public function __construct(
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        \MageWorx\RewardPoints\Helper\Data $helperData,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\Helper\Context $context
    ) {
        $this->helperData    = $helperData;
        $this->priceCurrency = $priceCurrency;
        $this->storeManager  = $storeManager;
    }

    /**
     * @param double $points
     * @param int|null $scopeId
     * @param string $scope Valid value: 'website' or 'store'
     * @return float
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getCurrencyByPoints($points, $scopeId = null, $scope = 'store')
    {
        if ($scope == 'store') {
            $websiteId = $this->storeManager->getStore($scopeId)->getWebsiteId();
        } else {
            $websiteId = $scopeId;
        }

        $rate = $this->helperData->getPointToCurrencyRate($websiteId);

        return $this->priceCurrency->round($points * $rate);
    }

    /**
     * @param double $value
     * @param int|null $store
     * @return float
     */
    public function getPointsByCurrency($value, $store = null)
    {
        $rate = $this->helperData->getCurrencyToPointRate($store);

        return $this->priceCurrency->round($value * $rate);
    }
}