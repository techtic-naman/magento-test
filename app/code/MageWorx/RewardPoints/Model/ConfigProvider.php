<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace MageWorx\RewardPoints\Model;

use MageWorx\RewardPoints\Model\Source\ApplyFor as ApplyForOptions;

class ConfigProvider implements \Magento\Checkout\Model\ConfigProviderInterface
{
    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Quote\Model\Quote
     */
    protected $quote;

    /**
     * @var \MageWorx\RewardPoints\Api\CustomerBalanceRepositoryInterface
     */
    protected $customerBalanceRepository;

    /**
     * @var \MageWorx\RewardPoints\Helper\Data
     */
    protected $helperData;

    /**
     * @var \MageWorx\RewardPoints\Helper\Price
     */
    protected $helperPrice;

    /**
     * @var PointCurrencyConverter
     */
    protected $pointCurrencyConverter;

    /**
     * @var ApplyForOptions
     */
    protected $applyForOptions;

    /**
     * @var CustomerBalance
     */
    protected $loadedCustomerBalance;

    /**
     * @var \Magento\Framework\Pricing\PriceCurrencyInterface
     */
    protected $priceCurrency;

    /**
     * ConfigProvider constructor.
     *
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \MageWorx\RewardPoints\Api\CustomerBalanceRepositoryInterface $customerBalanceRepository
     * @param \MageWorx\RewardPoints\Helper\Data $helperData
     * @param \MageWorx\RewardPoints\Helper\Price $helperPrice
     * @param PointCurrencyConverter $pointCurrencyConverter
     * @param ApplyForOptions $applyForOptions
     * @param \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency
     */
    public function __construct(
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \MageWorx\RewardPoints\Api\CustomerBalanceRepositoryInterface $customerBalanceRepository,
        \MageWorx\RewardPoints\Helper\Data $helperData,
        \MageWorx\RewardPoints\Helper\Price $helperPrice,
        \MageWorx\RewardPoints\Model\PointCurrencyConverter $pointCurrencyConverter,
        ApplyForOptions $applyForOptions,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency
    ) {
        $this->customerSession           = $customerSession;
        $this->storeManager              = $storeManager;
        $this->checkoutSession           = $checkoutSession;
        $this->customerBalanceRepository = $customerBalanceRepository;
        $this->helperData                = $helperData;
        $this->helperPrice               = $helperPrice;
        $this->pointCurrencyConverter    = $pointCurrencyConverter;
        $this->applyForOptions           = $applyForOptions;
        $this->priceCurrency             = $priceCurrency;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfig()
    {
        $paymentData = [
            'isAvailable'                      => $this->isAvailable(),
            'customerBalance'                  => (float)$this->getCustomerBalanceCurrencyAmount(),
            'used'                             => (float)$this->getQuote()->getMwRwrdpointsCurAmnt(),
            'label'                            => $this->getLabel(),
            'isAllowedCustomAmount'            => $this->helperData->isAllowedCustomPointsAmount(),
            'isDisplayCheckoutUpcomingMessage' => $this->helperData->isDisplayCheckoutUpcomingPoints(),
            'checkoutUpcomingMessage'          => $this->helperData->getCheckoutUpcomingPointsMessage(),
            'rate'                             => $this->getRate(),
        ];

        return [
            'payment' => [
                'mageworx_rewardpoints' => $paymentData
            ]
        ];
    }

    /**
     * @return bool
     */
    protected function isAvailable(): bool
    {
        if (!$this->helperData->isEnableForCustomer()) {
            return false;
        }

        if (!$this->helperData->getApplyForList()) {
            return false;
        }

        return (float)$this->getCustomerBalance()->getCurrencyAmount() > 0;
    }

    /**
     * @return \MageWorx\RewardPoints\Api\Data\CustomerBalanceInterface|CustomerBalance
     */
    protected function getCustomerBalance(): \MageWorx\RewardPoints\Api\Data\CustomerBalanceInterface
    {
        if (!$this->loadedCustomerBalance) {
            $this->loadedCustomerBalance = $this->customerBalanceRepository->getByCustomer(
                $this->customerSession->getCustomerId(),
                $this->storeManager->getStore()->getWebsiteId()
            );
        }

        return $this->loadedCustomerBalance;
    }

    /**
     * @return float|int
     */
    protected function getCustomerBalanceCurrencyAmount()
    {
        return $this->pointCurrencyConverter->getCurrencyByPoints($this->getCustomerBalance()->getPoints());
    }

    /**
     * @return \Magento\Quote\Model\Quote
     */
    protected function getQuote(): \Magento\Quote\Model\Quote
    {
        if (!$this->quote) {
            $this->quote = $this->checkoutSession->getQuote();
        }

        return $this->quote;
    }

    /**
     * @return \Magento\Framework\Phrase
     */
    protected function getLabel()
    {
        $message = __(
            'You have %1',
            $this->helperPrice->getFormattedPointsCurrency($this->getCustomerBalance()->getPoints())
        );

        $applyForList    = $this->helperData->getApplyForList();
        $applyForOptions = $this->applyForOptions->toArray();

        if (count($applyForList) != count($applyForOptions)) {
            $totalParts = [];

            foreach ($applyForList as $type) {
                if (!empty($applyForOptions[$type])) {
                    $totalParts[] = $applyForOptions[$type];
                }
            }

            if (!empty($totalParts)) {
                $andString = __('and');
                $message   .= '. ' . __('You can spend it to cover %1', implode(' ' . $andString . ' ', $totalParts));
            }
        }

        return $message;
    }

    /**
     * @return float
     */
    protected function getRate(): float
    {
        return $this->priceCurrency->convert($this->helperData->getPointToCurrencyRate());
    }
}
