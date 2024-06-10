<?php
/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_Marketplace
 * @author    Webkul Software Private Limited
 * @copyright Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */

namespace Webkul\Marketplace\Block\Account;

use Webkul\Marketplace\Model\SaleslistFactory;

class Earnings extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Webkul\Marketplace\Helper\Data
     */
    protected $mpHelper;
    /**
     * @var SaleslistFactory
     */
    protected $saleslistFactory;
    /**
     * Construct
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param SaleslistFactory $saleslistFactory
     * @param \Webkul\Marketplace\Helper\Data $mpHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        SaleslistFactory $saleslistFactory,
        \Webkul\Marketplace\Helper\Data $mpHelper,
        array $data = []
    ) {
        $this->mpHelper = $mpHelper;
        $this->saleslistFactory = $saleslistFactory;
        parent::__construct($context, $data);
    }
    /**
     * GetParmasDetail function is used to get request parameters
     *
     * @return array
     */
    public function getParmasDetail()
    {
        return $this->getRequest()->getParams();
    }
    
    /**
     * GetPeriodValues function is return the list of filter periods
     *
     * @return array
     */
    public function getPeriodValues()
    {
        return [
                    [
                        'value' => 'day',
                        'label' => __('Day')
                    ],
                    [
                        'value' => 'month',
                        'label' => __('Month')
                    ],
                    [
                        'value' => 'year',
                        'label' => __('Year')
                    ]
                ];
    }

    /**
     * Get data set
     *
     * @return array
     */
    public function getDatasets()
    {
        $limit = $this->getRequest()->getParam('period');
        $dataSet = [];
        $dataLabel = [];
        switch ($limit) {
            case 'month':
                list($dataSet, $dataLabel) = $this->getMonthlyData();
                break;
            case 'year':
                list($dataSet, $dataLabel) = $this->getYearlyData();
                break;
            default:
                list($dataSet, $dataLabel) = $this->getDailyData();
                break;
        }
        return [
            $this->prepareDataSet($this->mpHelper->arrayToJson($dataSet)),
            $this->mpHelper->arrayToJson($dataLabel)
        ];
    }

    /**
     * GetDailyData function is used to get data according to days
     *
     * @return array
     */
    protected function getDailyData()
    {
        $dataSet = [];
        $dataLabel = [];
        try {
            list($from, $to) = $this->getDateData();
            if ($to) {
                $todate = date_create($to);
                $to = $todate ? date_format($todate, 'Y-m-d 23:59:59') : null;
            }
            if (!$to) {
                $to = date('Y-m-d 23:59:59');
            }
            if ($from) {
                $fromdate = date_create($from);
                $from = $fromdate ? date_format($fromdate, 'Y-m-d 23:59:59') : null;
            }
            if (!$from) {
                $from = date('Y-m-d 23:59:59', strtotime($from));
            }
            $sellerId = $this->mpHelper->getCustomerId();
            $fromYear = $from ? date('Y', strtotime($from)) : date('Y');
            $fromMonth = $from ? (int)date('m', strtotime($from)) : 1;
            $fromDay = $from ? (int)date('d', strtotime($from)) : 1;
            $curryear = $to ? date('Y', strtotime($to)) : date('Y');
            $currMonth = $to ? (int)date('m', strtotime($to)) : date('m');
            $currDay = $to ? (int)date('d', strtotime($to)) : date('d');
            for ($startYear = $fromYear; $startYear <= $curryear; ++$startYear) {
                $months = 12;
                if ($startYear == $curryear) {
                    $months = $currMonth;
                }
                $monthStart = ($startYear == $fromYear && $from) ? $fromMonth : 1;

                $dailyArrData = $this->getDailyArrData(
                    $monthStart,
                    $months,
                    $fromYear,
                    $fromMonth,
                    $from,
                    $fromDay,
                    $startYear,
                    $currDay,
                    $currMonth,
                    $curryear,
                    $sellerId
                );

                $dataSet = $dailyArrData['data_set'];
                $dataLabel = $dailyArrData['data_label'];
            }
        } catch (\Exception $e) {
            $this->mpHelper->logDataInLogger("Block_Account_Earnings getDailyData : ".$e->getMessage());
        }
        return [$dataSet, $dataLabel];
    }

    /**
     * GetDailyArrData to calculate data_set and data_label.
     *
     * @param int $monthStart
     * @param int $months
     * @param int $fromYear
     * @param int $fromMonth
     * @param int $from
     * @param int $fromDay
     * @param int $startYear
     * @param int $currDay
     * @param int $currMonth
     * @param int $curryear
     * @param int $sellerId
     * @return array
     */
    public function getDailyArrData(
        $monthStart,
        $months,
        $fromYear,
        $fromMonth,
        $from,
        $fromDay,
        $startYear,
        $currDay,
        $currMonth,
        $curryear,
        $sellerId
    ) {
        $dataSet = [];
        $dataLabel = [];
        for ($monthValue = $monthStart; $monthValue <= $months; ++$monthValue) {
            $dayStart = ($startYear == $fromYear && $monthValue == $fromMonth && $from) ? $fromDay : 1;
            $days = $this->getMonthDays($monthValue, $startYear);
            if ($startYear == $curryear && $monthValue == $currMonth) {
                $days = $currDay;
            }
            for ($dayValue = $dayStart; $dayValue <= $days; ++$dayValue) {
                $dateFrom = $startYear.'-'.$monthValue.'-'.$dayValue.' 00:00:00';
                $dateTo = $startYear.'-'.$monthValue.'-'.$dayValue.' 23:59:59';
                $collection = $this->saleslistFactory->create()
                            ->getCollection()
                            ->addFieldToFilter(
                                'main_table.seller_id',
                                ['eq' => $sellerId]
                            )
                            ->addFieldToFilter(
                                'main_table.order_id',
                                ['neq' => 0]
                            )->addFieldToFilter(
                                'main_table.created_at',
                                ['datetime' => true, 'from' => $dateFrom, 'to' => $dateTo]
                            )->getPricebyorderData();
                $temp = 0;
                foreach ($collection as $record) {
                    // calculate order actual_seller_amount in base currency
                    $appliedCouponAmount = $record['applied_coupon_amount']*1;
                    $shippingAmount = $record['shipping_charges']*1;
                    $refundedShippingAmount = $record['refunded_shipping_charges']*1;
                    $totalshipping = $shippingAmount - $refundedShippingAmount;
                    if ($record['actual_seller_amount'] * 1) {
                        $taxShippingTotal = $totalshipping - $appliedCouponAmount;
                        $temp += $record['actual_seller_amount'] + $taxShippingTotal;
                    } else {
                        if ($totalshipping * 1) {
                            $temp += $totalshipping - $appliedCouponAmount;
                        }
                    }
                }
                if ($temp) {
                    $dataSet[] = $temp;
                    $dataLabel[] = $dayValue."/".$monthValue."/".$startYear;
                }
            }
        }

        return ['data_set' => $dataSet, 'data_label' => $dataLabel];
    }

    /**
     * GetMonthlyData function is used to get sale according to months
     *
     * @return array
     */
    protected function getMonthlyData()
    {
        $dataSet = [];
        $dataLabel = [];
        try {
            list($from, $to) = $this->getDateData();
            if ($to) {
                $todate = date_create($to);
                $to = date_format($todate, 'Y-m-d 23:59:59');
            }
            if (!$to) {
                $to = date('Y-m-d 23:59:59');
            }
            if ($from) {
                $fromdate = date_create($from);
                $from = date_format($fromdate, 'Y-m-d H:i:s');
            }
            if (!$from) {
                $from = date('Y-m-d 23:59:59', strtotime($from));
            }
            $sellerId = $this->mpHelper->getCustomerId();
            $fromYear = $from ? date('Y', strtotime($from)) : date('Y');
            $fromDay = $from ? (int)date('d', strtotime($from)) : 1;
            $fromMonth = $from ? (int)date('m', strtotime($from)) : 1;
            $curryear = $to ? date('Y', strtotime($to)) : date('Y');
            $currMonth = $to ? (int)date('m', strtotime($to)) : date('m');
            $currDay = $to ? (int)date('d', strtotime($to)) : date('d');
            for ($startYear = $fromYear; $startYear <= $curryear; ++$startYear) {
                $months = 12;
                if ($startYear == $curryear) {
                    $months = $currMonth;
                }
                $monthStart = ($startYear == $fromYear && $from) ? $fromMonth : 1;
                $monthlyArrData = $this->getMonthlyArrData(
                    $monthStart,
                    $months,
                    $fromYear,
                    $fromMonth,
                    $from,
                    $fromDay,
                    $startYear,
                    $currDay,
                    $currMonth,
                    $curryear,
                    $sellerId
                );
                $dataSet = $monthlyArrData['data_set'];
                $dataLabel = $monthlyArrData['data_label'];
            }
        } catch (\Exception $e) {
            $this->mpHelper->logDataInLogger("Block_Account_Earnings getMonthlyData : ".$e->getMessage());
        }
        return [$dataSet, $dataLabel];
    }

    /**
     * GetMonthlyArrData to calculate data_set and data_label.
     *
     * @param int $monthStart
     * @param int $months
     * @param int $fromYear
     * @param int $fromMonth
     * @param int $from
     * @param int $fromDay
     * @param int $startYear
     * @param int $currDay
     * @param int $currMonth
     * @param int $curryear
     * @param int $sellerId
     * @return array
     */
    public function getMonthlyArrData(
        $monthStart,
        $months,
        $fromYear,
        $fromMonth,
        $from,
        $fromDay,
        $startYear,
        $currDay,
        $currMonth,
        $curryear,
        $sellerId
    ) {
        $dataSet = [];
        $dataLabel = [];
        for ($monthValue = $monthStart; $monthValue <= $months; ++$monthValue) {
            $days = $this->getMonthDays($monthValue, $startYear);
            $dayStart = ($startYear == $fromYear && ($fromMonth == $monthValue) && $from) ? $fromDay : '01';
            $dayEnd = ($startYear == $curryear && ($currMonth == $monthValue) && $from) ? $currDay : $days;
            $dateFrom = $startYear.'-'.$monthValue.'-'.$dayStart.' 00:00:00';
            $dateTo = $startYear.'-'.$monthValue.'-'.$dayEnd.' 23:59:59';
            $collection = $this->saleslistFactory->create()
                        ->getCollection()
                        ->addFieldToFilter(
                            'main_table.seller_id',
                            ['eq' => $sellerId]
                        )
                        ->addFieldToFilter(
                            'main_table.order_id',
                            ['neq' => 0]
                        )->addFieldToFilter(
                            'main_table.created_at',
                            ['datetime' => true, 'from' => $dateFrom, 'to' => $dateTo]
                        )->getPricebyorderData();
            $temp = 0;
            foreach ($collection as $record) {
                // calculate order actual_seller_amount in base currency
                $appliedCouponAmount = $record['applied_coupon_amount']*1;
                $shippingAmount = $record['shipping_charges']*1;
                $refundedShippingAmount = $record['refunded_shipping_charges']*1;
                $totalshipping = $shippingAmount - $refundedShippingAmount;
                if ($record['actual_seller_amount'] * 1) {
                    $taxShippingTotal = $totalshipping - $appliedCouponAmount;
                    $temp += $record['actual_seller_amount'] + $taxShippingTotal;
                } else {
                    if ($totalshipping * 1) {
                        $temp += $totalshipping - $appliedCouponAmount;
                    }
                }
            }
            if ($temp) {
                $dataLabel[] = $monthValue."/".$startYear;
                $dataSet[] = $temp;
            }
        }

        return ['data_set' => $dataSet, 'data_label' => $dataLabel];
    }

    /**
     * GetYearlyDataMonthlyData function is used to get sale according to months
     *
     * @return array
     */
    protected function getYearlyData()
    {
        $dataSet = [];
        $dataLabel = [];
        try {
            list($from, $to) = $this->getDateData();
            $sellerId = $this->mpHelper->getCustomerId();
            if ($to) {
                $todate = date_create($to);
                $to = date_format($todate, 'Y-m-d 23:59:59');
            }
            if (!$to) {
                $to = date('Y-m-d 23:59:59');
            }
            if ($from) {
                $fromdate = date_create($from);
                $from = date_format($fromdate, 'Y-m-d H:i:s');
            }
            if (!$from) {
                $from = date('Y-m-d 23:59:59', strtotime($from));
            }
            $fromYear = $from ? date('Y', strtotime($from)) : date('Y');
            $fromMonth = $from ? (int)date('m', strtotime($from)) : 1;
            $fromDay = $from ? (int)date('d', strtotime($from)) : 1;
            $curryear = $to ? date('Y', strtotime($to)) : date('Y');
            $currMonth = $to ? (int)date('m', strtotime($to)) : date('m');
            $currDay = $to ? (int)date('d', strtotime($to)) : date('d');
            for ($start = $fromYear; $start <= $curryear; ++$start) {
                $monthStart = ($start == $fromYear) ? $fromMonth : '01';
                $monthEnd = ($start == $curryear) ? $currMonth : '12';
                $days = $this->getMonthDays($monthEnd, $start);
                $dayStart = ($start == $fromYear && $from) ? $fromDay : '01';
                $dayEnd = ($start == $curryear && $from) ? $currDay : $days;
                $dateFrom = $start.'-'.$monthStart.'-'.$dayStart.' 00:00:00';
                $dateTo = $start.'-'.$monthEnd.'-'.$dayEnd.' 23:59:59';
                $collection = $this->saleslistFactory->create()
                            ->getCollection()
                            ->addFieldToFilter(
                                'main_table.seller_id',
                                ['eq' => $sellerId]
                            )
                            ->addFieldToFilter(
                                'main_table.order_id',
                                ['neq' => 0]
                            )->addFieldToFilter(
                                'main_table.created_at',
                                ['datetime' => true, 'from' => $dateFrom, 'to' => $dateTo]
                            )->getPricebyorderData();
                $temp = 0;
                foreach ($collection as $record) {
                    // calculate order actual_seller_amount in base currency
                    $appliedCouponAmount = $record['applied_coupon_amount']*1;
                    $shippingAmount = $record['shipping_charges']*1;
                    $refundedShippingAmount = $record['refunded_shipping_charges']*1;
                    $totalshipping = $shippingAmount - $refundedShippingAmount;
                    if ($record['actual_seller_amount'] * 1) {
                        $taxShippingTotal = $totalshipping - $appliedCouponAmount;
                        $temp += $record['actual_seller_amount'] + $taxShippingTotal;
                    } else {
                        if ($totalshipping * 1) {
                            $temp += $totalshipping - $appliedCouponAmount;
                        }
                    }
                }
                if ($temp) {
                    $dataLabel[] = $start;
                    $dataSet[] = $temp;
                }
            }
        } catch (\Exception $e) {
            $this->mpHelper->logDataInLogger("Block_Account_Earnings getYearlyData : ".$e->getMessage());
        }
        return [$dataSet, $dataLabel];
    }
    
    /**
     * Prepare dataa set
     *
     * @param mixed $data
     * @return array
     */
    public function prepareDataSet($data)
    {
        return $data = "[{
                label: '".__('Sale')."',
                backgroundColor: color(window.chartColors.green).alpha(0.5).rgbString(),
                borderColor: window.chartColors.green,
                borderWidth: 1,
                data: $data
            }
        ]";
    }

    /**
     * Get date data
     *
     * @return array
     */
    public function getDateData()
    {
        $sellerId = $this->mpHelper->getCustomerId();
        $params = $this->getRequest()->getParams();
        $from = $params['from'] ?? '';
        $to = $params['to'] ?? '';
        if (!$from && !$to) {
            $collection = $this->saleslistFactory->create()
            ->getCollection()
            ->addFieldToFilter(
                'main_table.seller_id',
                ['eq' => $sellerId]
            )
            ->addFieldToFilter(
                'main_table.order_id',
                ['neq' => 0]
            )->setOrder('created_at', 'ASC')->getFirstItem();
            $from = $collection->getCreatedAt();
            $to = date("Y-m-d");
        }
        return [$from, $to];
    }

    /**
     * Get month days
     *
     * @param string $month
     * @param string $year
     * @return int
     */
    public function getMonthDays($month, $year)
    {
        $days = 28;
        if ((0 == $year % 4) && (0 != $year % 100) || (0 == $year % 400)) {
            $days = 29;
        }
        $monthsWithThirty = [4,6,9,11];
        $monthsWithThirtyOne = [1,3,5,7,8,10,12];
        if (in_array($month, $monthsWithThirty)) {
            $days = 30;
        } elseif (in_array($month, $monthsWithThirtyOne)) {
            $days = 31;
        }
        return $days;
    }
}
