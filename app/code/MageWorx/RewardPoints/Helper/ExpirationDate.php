<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace MageWorx\RewardPoints\Helper;

use Magento\Framework\Pricing\PriceCurrencyInterface;

class ExpirationDate extends \Magento\Framework\App\Helper\AbstractHelper
{
    const FORMAT_PERIOD_INT    = 1;
    const FORMAT_PERIOD_STRING = 2;
    const FORMAT_PERIOD_FULL   = 3;

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
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $date;

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
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $date,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        \MageWorx\RewardPoints\Helper\Data $helperData,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \MageWorx\RewardPoints\Model\PointCurrencyConverter $pointConverter,
        \Magento\Framework\App\Helper\Context $context
    ) {
        $this->date           = $date;
        $this->helperData     = $helperData;
        $this->priceCurrency  = $priceCurrency;
        $this->storeManager   = $storeManager;
        $this->pointConverter = $pointConverter;
        parent::__construct($context);
    }

    /**
     * @param int $period
     * @param null|string $startDate
     * @return string
     */
    public function getExpirationDateFromPeriod(int $period, string $startDate = null): string
    {
        if ($startDate) {
            $date = new \DateTime($startDate);
        } else {
            $date = $this->date->date();
        }

        $dateAsString = $date->modify($period . ' day')->format('Y-m-d');

        return $dateAsString;
    }

    /**
     * @param string $date
     * @param int $format
     * @return int|\Magento\Framework\Phrase|string
     * @throws \Exception
     */
    public function getExpirationPeriodFromDate(string $date, int $format = self::FORMAT_PERIOD_INT)
    {
        $sourceDate  = new \DateTime($date);
        $currentDate = $this->date->date();
        $period      = $currentDate->diff($sourceDate)->format('%r%a') + 1;

        switch ($format) {
            case self::FORMAT_PERIOD_INT:
                $result = $period > 0 ? $period : __('Expired');
                break;
            case self::FORMAT_PERIOD_STRING:
                if ($period > 0) {
                    $result = __(
                        '%num days left',
                        [
                            'num' => '<b>' . $period . '</b>'
                        ]
                    );
                } else {
                    $result = __('Expired');
                }
                break;
            case self::FORMAT_PERIOD_FULL:
                if ($period > 0) {
                    $result = __(
                        '%date (%num days left)',
                        [
                            'date' => $date,
                            'num'  => '<b>' . $period . '</b>'
                        ]
                    );
                } else {
                    $result = __(
                        '%date (Expired)',
                        [
                            'date' => $date
                        ]
                    );
                }
                break;
            default:
                throw new \InvalidArgumentException('Invalid format for reward points expiration date');
        }

        return $result;
    }

    /**
     * @param string $expirationDate
     * @return mixed
     */
    public function formatExpirationDate(string $expirationDate)
    {
        if ($expirationDate) {
            $date = new \DateTime($expirationDate);

            return __('Expiration date is %1', $date->format('d-m-Y'));
        }

        return '';
    }

    /**
     * @param \MageWorx\RewardPoints\Api\Data\CustomerBalanceInterface $customerBalance
     * @param null|false|int $expirationPeriod
     */
    public function getExpirationDateForBalance(
        \MageWorx\RewardPoints\Api\Data\CustomerBalanceInterface $customerBalance,
        $expirationPeriod
    ) {
        if ($expirationPeriod === null) {
            $expirationDate = $customerBalance->getExpirationDate();
        } elseif ($expirationPeriod === false) {
            $expirationDate = null;
        } else {
            $expirationDate = $this->getExpirationDateFromPeriod((int)$expirationPeriod);
        }

        return $expirationDate;
    }

    /**
     * Convert expiration period value from form for manual update points
     *
     * @param string $expirationPeriod
     * @return bool|int|null
     */
    public function convertExpirationPeriodFormValue(string $expirationPeriod)
    {
        if ($expirationPeriod === '0') {
            return null;
        }

        if ($expirationPeriod === '') {
            return false;
        }

        return (int)$expirationPeriod;
    }
}
