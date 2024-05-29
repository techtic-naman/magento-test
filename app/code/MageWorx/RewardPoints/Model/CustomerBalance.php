<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\RewardPoints\Model;

use MageWorx\RewardPoints\Api\Data\CustomerBalanceInterface;

class CustomerBalance extends \Magento\Framework\Model\AbstractModel implements CustomerBalanceInterface
{
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Framework\Locale\CurrencyInterface
     */
    protected $localeCurrency;

    /**
     * @var \MageWorx\RewardPoints\Model\PointCurrencyConverter
     */
    protected $pointCurrencyConverter;

    /**
     * @var string
     */
    protected $websiteCurrencyCode;

    /**
     * @var double
     */
    protected $currencyAmount;

    /**
     * CustomerBalance constructor.
     *
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param PointCurrencyConverter $pointCurrencyConverter
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Locale\CurrencyInterface $localeCurrency
     * @param \MageWorx\RewardPoints\Model\ResourceModel\CustomerBalance $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \MageWorx\RewardPoints\Model\PointCurrencyConverter $pointCurrencyConverter,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Locale\CurrencyInterface $localeCurrency,
        \MageWorx\RewardPoints\Model\ResourceModel\CustomerBalance $resource,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->storeManager           = $storeManager;
        $this->localeCurrency         = $localeCurrency;
        $this->pointCurrencyConverter = $pointCurrencyConverter;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init(\MageWorx\RewardPoints\Model\ResourceModel\CustomerBalance::class);
    }

    /**
     * {@inheritdoc}
     */
    public function getCustomerId()
    {
        return (int)$this->getData('customer_id');
    }

    /**
     * {@inheritdoc}
     */
    public function setCustomerId($customerId)
    {
        return $this->setData('customer_id', $customerId);
    }

    /**
     * {@inheritdoc}
     */
    public function getWebsiteId()
    {
        return (int)$this->getData('website_id');
    }

    /**
     * {@inheritdoc}
     */
    public function setWebsiteId($websiteId)
    {
        return $this->setData('website_id', $websiteId);
    }

    /**
     * {@inheritdoc}
     */
    public function getPoints()
    {
        return (double)$this->getData('points');
    }

    /**
     * {@inheritdoc}
     */
    public function setPoints($points)
    {
        return $this->setData('points', $points);
    }

    /**
     * {@inheritdoc}
     */
    public function getExpirationDate()
    {
        return $this->getData('expiration_date');
    }

    /**
     * {@inheritdoc}
     */
    public function setExpirationDate($date = null)
    {
        return $this->setData('expiration_date', $date);
    }

    /**
     * @return float|null
     */
    public function getCurrencyAmount()
    {
        if ($this->currencyAmount === null) {
            $this->prepareCurrencyAmount();
        }

        return $this->currencyAmount;
    }

    /**
     * @return string
     */
    public function getFormatedCurrencyAmount()
    {
        $currencyAmount = $this->localeCurrency->getCurrency(
            $this->getWebsiteCurrencyCode()
        )->toCurrency(
            $this->getCurrencyAmount()
        );

        return $currencyAmount;
    }

    /**
     * @return $this
     */
    protected function prepareCurrencyAmount()
    {
        $amount               = $this->pointCurrencyConverter->getCurrencyByPoints(
            $this->getPoints(),
            $this->getWebsiteId(),
            'website'
        );

        $this->currencyAmount = (double)$amount;

        return $this;
    }

    /**
     * @return string
     */
    protected function getWebsiteCurrencyCode()
    {
        if (!$this->websiteCurrencyCode) {
            $this->websiteCurrencyCode = $this->storeManager->getWebsite($this->getWebsiteId())->getBaseCurrencyCode();
        }

        return $this->websiteCurrencyCode;
    }

    /**
     * {@inheritdoc}
     */
    public function beforeSave()
    {
        if ($this->getExpirationDate() && !(double)$this->getPoints()) {
            $this->setExpirationDate(null);
        }

        return parent::beforeSave();
    }
}
