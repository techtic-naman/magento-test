<?php
/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_Mpmembership
 * @author    Webkul Software Private Limited
 * @copyright Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */

namespace Webkul\Mpmembership\Helper;

use Magento\Store\Model\StoreManagerInterface;
use Magento\Directory\Model\Currency;
use Magento\Framework\Locale\CurrencyInterface;
use Webkul\Mpmembership\Model\ResourceModel\Product\CollectionFactory;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable;
use Webkul\Mpmembership\Api\TransactionRepositoryInterface;
use Webkul\Mpmembership\Model\Transaction;
use Magento\Framework\View\Element\Block\ArgumentInterface;

/**
 * Mpmembership data helper.
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper implements ArgumentInterface
{
    /**
     * @var \Magento\Catalog\Model\ProductRepository
     */
    protected $productRepository;

    /**
     * @var \Magento\Backend\Model\UrlInterface
     */
    protected $backendUrl;

    /**
     * @var TransactionRepositoryInterface
     */
    protected $transactionRepository;

    /**
     * @var Configurable
     */
    protected $catalogProductTypeConfigurable;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var Currency
     */
    protected $currency;

    /**
     * @var CurrencyInterface
     */
    protected $localeCurrency;

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var \Webkul\Mpmembership\Model\ResourceModel\Transaction\CollectionFactory
     */
    protected $transactionCollection;

    /**
     * @var CustomerRepositoryInterface
     */
    protected $customerRepository;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $timezone;

    /**
     * @var \Webkul\Marketplace\Helper\Data
     */
    protected $mpHelper;

    /**
     * @var \Webkul\Mpmembership\Logger\Logger
     */
    protected $logger;

    /**
     * @var \Magento\Framework\Pricing\PriceCurrencyInterface
     */
    protected $priceCurrency;

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param StoreManagerInterface $storeManager
     * @param Currency $currency
     * @param CurrencyInterface $localeCurrency
     * @param CollectionFactory $collectionFactory
     * @param \Webkul\Mpmembership\Model\ResourceModel\Transaction\CollectionFactory $transactionCollection
     * @param CustomerRepositoryInterface $customerRepository
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezoneInterface
     * @param \Webkul\Marketplace\Helper\Data $mpHelper
     * @param \Webkul\Mpmembership\Logger\Logger $logger
     * @param \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency
     * @param Configurable $catalogProductTypeConfigurable
     * @param \Magento\Catalog\Model\ProductRepository $productRepository
     * @param \Magento\Backend\Model\UrlInterface $backendUrl
     * @param TransactionRepositoryInterface $transactionRepository
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        StoreManagerInterface $storeManager,
        Currency $currency,
        CurrencyInterface $localeCurrency,
        CollectionFactory $collectionFactory,
        \Webkul\Mpmembership\Model\ResourceModel\Transaction\CollectionFactory $transactionCollection,
        CustomerRepositoryInterface $customerRepository,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezoneInterface,
        \Webkul\Marketplace\Helper\Data $mpHelper,
        \Webkul\Mpmembership\Logger\Logger $logger,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        Configurable $catalogProductTypeConfigurable,
        \Magento\Catalog\Model\ProductRepository $productRepository,
        \Magento\Backend\Model\UrlInterface $backendUrl,
        TransactionRepositoryInterface $transactionRepository
    ) {
        parent::__construct($context);
        $this->storeManager = $storeManager;
        $this->currency = $currency;
        $this->localeCurrency = $localeCurrency;
        $this->collectionFactory = $collectionFactory;
        $this->transactionCollection = $transactionCollection;
        $this->customerRepository = $customerRepository;
        $this->timezone = $timezoneInterface;
        $this->mpHelper = $mpHelper;
        $this->logger = $logger;
        $this->priceCurrency = $priceCurrency;
        $this->catalogProductTypeConfigurable = $catalogProductTypeConfigurable;
        $this->productRepository = $productRepository;
        $this->backendUrl = $backendUrl;
        $this->transactionRepository = $transactionRepository;
    }

    /**
     * GetConfigFeeAppliedFor used to get spitcart is enable or not
     *
     * @return int [returns 0 if disable else return 1]
     */
    public function getConfigFeeAppliedFor()
    {
        try {
            return $this->scopeConfig->getValue(
                'marketplace/mpmembership_settings/feeappliedfor',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );
        } catch (\Exception $e) {
            $this->logDataInLogger(
                "Helper_Data_getConfigFeeAppliedFor Exception : ".$e->getMessage()
            );
        }
    }

    /**
     * GetConfigFeeType
     *
     * @return int
     */
    public function getConfigFeeType()
    {
        try {
            return $this->scopeConfig->getValue(
                'marketplace/mpmembership_settings/type_of_fee',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );
        } catch (\Exception $e) {
            $this->logDataInLogger(
                "Helper_Data_getConfigFeeType Exception : ".$e->getMessage()
            );
        }
    }

    /**
     * GetConfigFeeAmount
     *
     * @return mixed
     */
    public function getConfigFeeAmount()
    {
        try {
            return $this->scopeConfig->getValue(
                'marketplace/mpmembership_settings/fee_amount',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );
        } catch (\Exception $e) {
            $this->logDataInLogger(
                "Helper_Data_getConfigFeeAmount Exception : ".$e->getMessage()
            );
        }
    }

    /**
     * GetConfigSandboxStatus
     *
     * @return boolean
     */
    public function getConfigSandboxStatus()
    {
        try {
            return $this->scopeConfig->getValue(
                'marketplace/mpmembership_settings/sandbox',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );
        } catch (\Exception $e) {
            $this->logDataInLogger(
                "Helper_Data_getConfigSandboxStatus Exception : ".$e->getMessage()
            );
        }
    }

    /**
     * GetConfigDefaultTime
     *
     * @return mixed
     */
    public function getConfigDefaultTime()
    {
        try {
            return $this->scopeConfig->getValue(
                'marketplace/mpmembership_settings/defaulttimeallowed',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );
        } catch (\Exception $e) {
            $this->logDataInLogger(
                "Helper_Data_getConfigDefaultTime Exception : ".$e->getMessage()
            );
        }
    }

    /**
     * GetConfigDefaultProduct
     *
     * @return int
     */
    public function getConfigDefaultProduct()
    {
        try {
            return $this->scopeConfig->getValue(
                'marketplace/mpmembership_settings/defaultproductallowed',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );
        } catch (\Exception $e) {
            $this->logDataInLogger(
                "Helper_Data_getConfigDefaultProduct Exception : ".$e->getMessage()
            );
        }
    }

    /**
     * GetConfigPaypalId
     *
     * @return string
     */
    public function getConfigPaypalId()
    {
        try {
            return $this->scopeConfig->getValue(
                'marketplace/mpmembership_settings/paypalid',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );
        } catch (\Exception $e) {
            $this->logDataInLogger(
                "Helper_Data_getConfigPaypalId Exception : ".$e->getMessage()
            );
        }
    }

    /**
     * GetConfigCheckType
     *
     * @return int
     */
    public function getConfigCheckType()
    {
        try {
            return $this->scopeConfig->getValue(
                'marketplace/mpmembership_settings/checktype',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );
        } catch (\Exception $e) {
            $this->logDataInLogger(
                "Helper_Data_getConfigCheckType Exception : ".$e->getMessage()
            );
        }
    }

    /**
     * IsModuleEnabled
     *
     * @return boolean
     */
    public function isModuleEnabled()
    {
        try {
            return $this->scopeConfig->getValue(
                'marketplace/mpmembership_settings/enabled',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );
        } catch (\Exception $e) {
            $this->logDataInLogger(
                "Helper_Data_isModuleEnabled Exception : ".$e->getMessage()
            );
        }
    }

    /**
     * GetBaseCurrencyCode
     *
     * Get store base currency code
     *
     * @return string
     */
    public function getBaseCurrencyCode()
    {
        try {
            return $this->storeManager->getStore()->getBaseCurrencyCode();
        } catch (\Exception $e) {
            $this->logDataInLogger(
                "Helper_Data_getBaseCurrencyCode Exception : ".$e->getMessage()
            );
        }
    }

    /**
     * GetCurrentCurrencyCode
     *
     * @return string
     */
    public function getCurrentCurrencyCode()
    {
        try {
            return $this->storeManager->getStore()->getCurrentCurrencyCode();
        } catch (\Exception $e) {
            $this->logDataInLogger(
                "Helper_Data_getCurrentCurrencyCode Exception : ".$e->getMessage()
            );
        }
    }

    /**
     * GetAllowedCurrencies
     *
     * @return array
     */
    public function getAllowedCurrencies()
    {
        try {
            return $this->storeManager->getStore()->getAllowedCurrencies();
        } catch (\Exception $e) {
            $this->logDataInLogger(
                "Helper_Data_getAllowedCurrencies Exception : ".$e->getMessage()
            );
        }
    }

    /**
     * GetCurrencyRates
     *
     * @param string $baseCurrencyCode
     * @param array $allowedCurrenciesList
     * @return mixed
     */
    public function getCurrencyRates($baseCurrencyCode, $allowedCurrenciesList)
    {
        try {
            return $this->currency->getCurrencyRates($baseCurrencyCode, $allowedCurrenciesList);
        } catch (\Exception $e) {
            $this->logDataInLogger(
                "Helper_Data_getCurrencyRates Exception : ".$e->getMessage()
            );
        }
    }

    /**
     * GetCurrentCurrencySymbol
     *
     * @return mixed
     */
    public function getCurrentCurrencySymbol()
    {
        try {
            $currencyCode = $this->getCurrentCurrencyCode();
            return $this->getCurrencySymbol($currencyCode);
        } catch (\Exception $e) {
            $this->logDataInLogger(
                "Helper_Data_getCurrentCurrencySymbol Exception : ".$e->getMessage()
            );
        }
    }

    /**
     * GetCurrencySymbol
     *
     * Retrieve currency Symbol.
     *
     * @param mixed $currencyCode
     * @return string
     */
    public function getCurrencySymbol($currencyCode)
    {
        try {
            return $this->localeCurrency->getCurrency($currencyCode)->getSymbol();
        } catch (\Exception $e) {
            $this->logDataInLogger(
                "Helper_Data_getCurrencySymbol Exception : ".$e->getMessage()
            );
        }
    }

    /**
     * GetPendingProductIds
     *
     * @param int $sellerId
     * @return array
     */
    public function getPendingProductIds($sellerId)
    {
        try {
            $transactionModel = $this->collectionFactory->create()
                ->addFieldToFilter(
                    'seller_id',
                    ['eq'  => $sellerId]
                )->addFieldToFilter(
                    'reference_number',
                    ['neq'  => '']
                )->addFieldToFilter(
                    'transaction_status',
                    ['eq'  => 'pending']
                )->addFieldToSelect('product_ids');

            if ($transactionModel->getSize()) {
                $proIds = $transactionModel->getColumnValues('product_ids');
                $proIds = implode(",", $proIds);
                $proIds = explode(",", $proIds);
                return array_unique($proIds);
            }
            return [];
        } catch (\Exception $e) {
            $this->logDataInLogger(
                "Helper_Data_getPendingProductIds Exception : ".$e->getMessage()
            );
        }
    }

    /**
     * AuthenticateData
     *
     * @param array $data
     * @return boolean
     */
    public function authenticateData($data)
    {
        try {
            $merchantPaypalId = $this->getConfigPaypalId();
            $customerId = $this->getSellerId();
            $pendingIds = $this->getPendingProductIds($customerId);

            if (isset($data['business'])
                && isset($data['product_id'])
                && !empty($data['product_id'])
            ) {
                if ($data['business'] == $merchantPaypalId) {
                    foreach ($data['product_id'] as $productIds) {
                        if (!empty($pendingIds)
                            && is_array($pendingIds)
                            && in_array($productIds, $pendingIds)
                        ) {
                            return false;
                        }
                    }
                    return true;
                } else {
                    return false;
                }
            } else {
                return false;
            }
        } catch (\Exception $e) {
            $this->logDataInLogger(
                "Helper_Data_authenticateData Exception : ".$e->getMessage()
            );
            return false;
        }
    }

    /**
     * CheckIfProductAvailableToPayFee
     *
     * @param array $productIds
     * @param array $pendingProductIds
     * @return boolean
     */
    public function checkIfProductAvailableToPayFee($productIds, $pendingProductIds)
    {
        $flag = false;
        try {
            if (!empty($productIds) && !empty($pendingProductIds)) {
                $productIds = array_unique($productIds);
                $pendingProductIds = array_unique($pendingProductIds);
                sort($productIds);
                sort($pendingProductIds);
                if ($productIds==$pendingProductIds) {
                    $flag = true;
                }
            }
        } catch (\Exception $e) {
            $this->logDataInLogger(
                "Helper_Data_checkIfProductAvailableToPayFee Exception : ".$e->getMessage()
            );
        }
        return $flag;
    }

    /**
     * GetPermission used to get data according to which notice will be
     *
     * Displayed when seller going to add product
     *
     * @return array
     */
    public function getPermission()
    {
        $data = [];
        try {
            $customerId = $this->getSellerId();

            $sellerTransactions = $this->transactionCollection->create()
                ->addFieldToFilter(
                    'seller_id',
                    ['eq' => $customerId]
                )->addFieldToFilter(
                    'transaction_id',
                    ['neq' => '']
                )->setOrder('transaction_date', 'ASC');

            $timeAndProducts = Transaction::TIME_AND_PRODUCTS;
            $time = Transaction::TIME;
            $products = Transaction::PRODUCTS;

            $expire = false;
            $transactionId = "";
            $qty = "";
            $expireTime = "";

            if ($sellerTransactions->getSize()) {
                $status = true;

                foreach ($sellerTransactions as $partner) {
                    $qty = "";
                    $expireTime = "";
                    $status = true;
                    $transactionId = $partner->getTransactionId();

                    $noOfProducts = $partner->getNoOfProducts();
                    $remainingProducts = $partner->getRemainingProducts();
                    if ($partner->getCheckType() == $timeAndProducts) {
                        $check = $timeAndProducts;
                        if ($partner->getExpiryDate() > date('Y-m-d h:i:s')
                            && $remainingProducts < $noOfProducts
                        ) {
                            $expire = false;
                            $qty = $noOfProducts - $remainingProducts;
                            $expireTime = $partner->getExpiryDate();
                            break;
                        } else {
                            $expire = true;
                            $qty = "";
                            $expireTime = "";
                        }
                    } elseif ($partner->getCheckType() == $time) {
                        $check = $time;
                        if ($partner->getExpiryDate() > date('Y-m-d h:i:s')) {
                            $expire = false;
                            $expireTime = $partner->getExpiryDate();
                            break;
                        } else {
                            $expire = true;
                            $expireTime = "";
                        }
                    } elseif ($partner->getCheckType() == $products) {
                        $check = $products;

                        if ($remainingProducts < $noOfProducts) {
                            $expire = false;
                            $qty = $noOfProducts - $remainingProducts;
                            break;
                        } else {
                            $expire = true;
                            $qty = "";
                        }
                    } else {
                        $expire = true;
                        $qty = "";
                        $check = "";
                    }
                }
            } else {
                $expire = true;
                $status = false;
                $check = "";
            }

            if ($expireTime!=="") {
                $expireTime = $this->formatDateByDateTime($expireTime);
            }

            $data = [
                'status' => $status,
                'qty' => $qty,
                'expire' => $expire,
                'check' => $check,
                'time' => $expireTime,
                'transaction' => $transactionId
            ];
        } catch (\Exception $e) {
            $this->logDataInLogger(
                "Helper_Data_getPermission Exception : ".$e->getMessage()
            );
        }
        return $data;
    }

    /**
     * ConvertDateTimeToConfigTimeZone convert Datetime from one zone to another
     *
     * @param string $dateTime which we want to convert
     */
    public function convertDateTimeToConfigTimeZone($dateTime = "")
    {
        try {
            // Date for a specific date/time:
            $date = new \DateTime($dateTime);

            // Convert timezone
            $tz = new \DateTimeZone($this->timezone->getConfigTimezone());
            $date->setTimeZone($tz);

            // Output date after
            return strtotime($date->format('d-m-Y,h:i:s a'));
        } catch (\Exception $e) {
            $this->logDataInLogger(
                "Helper_Data_convertDateTimeToConfigTimeZone Exception : ".$e->getMessage()
            );
        }
    }

    /**
     * FormatDateByDateTime
     *
     * @param string $dateTime
     * @param boolean $flag
     * @return string
     */
    public function formatDateByDateTime($dateTime, $flag = true)
    {
        try {
            if ($flag) {
                $timeStamp = $this->convertDateTimeToConfigTimeZone($dateTime);
            } else {
                $timeStamp = strtotime($dateTime);
            }
            return date("M j, Y g:i:s a", $timeStamp);
        } catch (\Exception $e) {
            $this->logDataInLogger(
                "Helper_Data_formatDateByDateTime Exception : ".$e->getMessage()
            );
        }
    }

    /**
     * GetCustomerById
     *
     * @param int $id
     * @return object
     */
    public function getCustomerById($id)
    {
        try {
            return $this->customerRepository->getById($id);
        } catch (\Exception $e) {
            $this->logDataInLogger(
                "Helper_Data_getCustomerById Exception : ".$e->getMessage()
            );
        }
    }

    /**
     * GetSellerId
     *
     * @return int
     */
    public function getSellerId()
    {
        try {
            return $this->mpHelper->getCustomerId();
        } catch (\Exception $e) {
            $this->logDataInLogger(
                "Helper_Data_getSellerId Exception : ".$e->getMessage()
            );
        }
    }

    /**
     * GetValidTransaction
     *
     * @param array $data
     * @return array
     */
    public function getValidTransaction($data)
    {
        try {
            $disabled = false;
            $defaultMsg = __(
                "you can not add more products, your pack is expired. Please pay fee to add more products"
            );
            if ($data['status']) {
                if ($data['check'] == Transaction::PRODUCTS) {
                    if ($data['expire']) {
                        $disabled = true;
                        $message = $defaultMsg;
                    } else {
                        $disabled = false;
                        $message = __(
                            "you are allowed only %1 products to add in %2 transaction",
                            $data['qty'],
                            $data['transaction']
                        );
                    }
                } elseif ($data['check'] == Transaction::TIME) {
                    if ($data['expire']) {
                        $disabled = true;
                        $message = $defaultMsg;
                    } else {
                        $disabled = false;
                        $message = __(
                            "you are allowed to add products till %1 for %2 transaction",
                            $data['time'],
                            $data['transaction']
                        );
                    }
                } elseif ($data['check'] == Transaction::TIME_AND_PRODUCTS) {
                    if ($data['expire']) {
                        $disabled = true;
                        $message = $defaultMsg;
                    } else {
                        $disabled = false;
                        $message = __(
                            "you are allowed only %1 product(s) to add till %2 for %3 transaction",
                            $data['qty'],
                            $data['time'],
                            $data['transaction']
                        );
                    }
                } else {
                    $disabled = true;
                    $message = __("please pay fee to add products");
                }
            } else {
                $disabled = true;
                $message = __("please pay fee to add products");
            }

            return [
                'status' => $disabled,
                'msg' => $message
            ];
        } catch (\Exception $e) {
            $this->logDataInLogger(
                "Helper_Data_getValidTransaction Exception : ".$e->getMessage()
            );
        }
    }

    /**
     * FormatPrice
     *
     * @param int|float $price
     * @param mixed $fromCurrency
     * @return mixed
     */
    public function formatPrice($price, $fromCurrency)
    {
        try {
            return $this->priceCurrency->round($price);
        } catch (\Exception $e) {
            $this->logDataInLogger(
                "Helper_Data_formatPrice Exception : ".$e->getMessage()
            );
        }
    }

    /**
     * GetFormattedPrice
     *
     * @param int|float $price
     * @return int|float
     */
    public function getFormattedPrice($price)
    {
        try {
            $price = $this->priceCurrency->format($price, false);
        } catch (\Exception $e) {
            $this->logDataInLogger(
                "Helper_Data_getFormattedPrice Exception : ".$e->getMessage()
            );
        }
        return $price;
    }

    /**
     * ConvertFromBaseToCurrentCurrency
     *
     * @param int|float $amount
     * @return int|float
     */
    public function convertFromBaseToCurrentCurrency($amount)
    {
        try {
            if ($this->getCurrentCurrencyCode() !== $this->getBaseCurrencyCode()) {
                $amount = $this->storeManager->getStore()->getBaseCurrency()->convert(
                    $amount,
                    $this->storeManager->getStore()->getCurrentCurrency()
                );
            }
        } catch (\Exception $e) {
            $this->logDataInLogger(
                "Helper_Data_convertFromBaseToCurrentCurrency Exception : ".$e->getMessage()
            );
        }
        return $amount;
    }

    /**
     * LogDataInLogger logs data in Webkul_Mpmembership.log file
     *
     * @param string $data
     * @return void
     */
    public function logDataInLogger($data)
    {
        $this->logger->info($data);
    }

    /**
     * GetValidProductIds
     *
     * @param int $sellerId
     * @return array
     */
    public function getValidProductIds($sellerId)
    {
        $productIds = [];
        try {
            $transactionModel = $this->collectionFactory->create()
                ->addFieldToFilter(
                    'seller_id',
                    ['eq'  => $sellerId]
                )->addFieldToFilter(
                    'reference_number',
                    ['neq'  => '']
                )->addFieldToFilter(
                    'transaction_id',
                    ['neq'  => '']
                )->addFieldToFilter(
                    'ipn_transaction_id',
                    ['neq'  => '']
                )->addFieldToFilter(
                    'transaction_status',
                    ['eq'  => 'verified']
                )->addFieldToSelect('product_ids');

            if ($transactionModel->getSize()) {
                $proIds = $transactionModel->getColumnValues('product_ids');
                $proIds = implode(",", $proIds);
                $proIds = explode(",", $proIds);
                $productIds = array_unique($proIds);
            }
        } catch (\Exception $e) {
            $this->logDataInLogger(
                "Helper_Data_getValidProductIds Exception : ".$e->getMessage()
            );
        }
        return $productIds;
    }

    /**
     * GetParentIdsByChildId
     *
     * @param int $childId
     * @return array
     */
    public function getParentIdsByChildId($childId)
    {
        $ids = [];
        try {
            $ids = $this->catalogProductTypeConfigurable->getParentIdsByChild($childId);
        } catch (\Exception $e) {
            $this->logDataInLogger(
                "Helper_Data_getParentIdsByChildId Exception : ".$e->getMessage()
            );
        }
        return $ids;
    }

    /**
     * GetProducts used to get product names with links by product ids
     *
     * @param string $ids [contains product ids as comma seperated]
     *
     * @return string [returns comma seperated product SKUs]
     */
    public function getProducts($ids)
    {
        $html = "";
        try {
            if ($ids) {
                $productIds = explode(",", $ids);
                if (!empty($productIds)) {
                    foreach ($productIds as $id) {
                        $_product = $this->productRepository->getById($id);
                        if ($_product && $_product->getId()) {
                            $html .= "<a style='display:block' href='".$this->backendUrl->getUrl(
                                'catalog/product/edit',
                                ['id' => $_product->getId()]
                            )."' target='blank' title='".__('View Product')."'>".$_product->getName().'</a>';
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            $this->logDataInLogger(
                "Helper_Data_getProducts Exception : ".$e->getMessage()
            );
        }
        return $html;
    }

    /**
     * UpdateTransactionType
     *
     * @param int $id
     * @param int $type
     * @return void
     */
    public function updateTransactionType($id, $type)
    {
        try {
            $data = $this->transactionRepository->getById($id);
            $data->setTransactionType($type)->save();
        } catch (\Exception $e) {
            $this->logDataInLogger(
                "Helper_Data_updateTransactionType Exception : ".$e->getMessage()
            );
        }
    }

    /**
     * ValidateTransactionForTimeAndProducts
     *
     * @param Webkul\Mpmembership\Model\Transaction $transaction
     * @return void
     */
    public function validateTransactionForTimeAndProducts($transaction)
    {
        try {
            if ($transaction->getExpiryDate() > date('Y-m-d h:i:s')
                && $transaction->getRemainingProducts() < $transaction->getNoOfProducts()
            ) {
                $this->updateTransactionType($transaction->getId(), Transaction::VALID);
            } elseif ($transaction->getExpiryDate() <= date('Y-m-d h:i:s')) {
                $this->updateTransactionType($transaction->getId(), Transaction::TIME_EXPIRED);
            } elseif ($transaction->getRemainingProducts() == $transaction->getNoOfProducts()) {
                $this->updateTransactionType($transaction->getId(), Transaction::PRODUCT_LIMIT_COMPLETED);
            }
        } catch (\Exception $e) {
            $this->logDataInLogger(
                "Helper_Data_validateTransactionForTimeAndProducts Exception : ".$e->getMessage()
            );
        }
    }

    /**
     * ValidateTransactionForTime
     *
     * @param Webkul\Mpmembership\Model\Transaction $transaction
     * @return void
     */
    public function validateTransactionForTime($transaction)
    {
        try {
            if ($transaction->getExpiryDate() > date('Y-m-d h:i:s')) {
                $this->updateTransactionType($transaction->getId(), Transaction::VALID);
            } elseif ($transaction->getExpiryDate() <= date('Y-m-d h:i:s')) {
                $this->updateTransactionType($transaction->getId(), Transaction::TIME_EXPIRED);
            }
        } catch (\Exception $e) {
            $this->logDataInLogger(
                "Helper_Data_validateTransactionForTime Exception : ".$e->getMessage()
            );
        }
    }

    /**
     * ValidateTransactionForProducts
     *
     * @param Webkul\Mpmembership\Model\Transaction $transaction
     * @return void
     */
    public function validateTransactionForProducts($transaction)
    {
        try {
            if ($transaction->getRemainingProducts() < $transaction->getNoOfProducts()) {
                $this->updateTransactionType($transaction->getId(), Transaction::VALID);
            } elseif ($transaction->getRemainingProducts() == $transaction->getNoOfProducts()) {
                $this->updateTransactionType($transaction->getId(), Transaction::PRODUCT_LIMIT_COMPLETED);
            }
        } catch (\Exception $e) {
            $this->logDataInLogger(
                "Helper_Data_validateTransactionForProducts Exception : ".$e->getMessage()
            );
        }
    }

    /**
     * Get formatted by price and currency.
     *
     * @param int $price
     *
     * @return int
     */
    public function getPriceCurrencyFormat($price)
    {
        $currencyCode = $this->getBaseCurrencyCode();
        $currency = $this->getCurrencySymbol($currencyCode);
        return $this->priceCurrency->format(
            $price,
            true,
            2,
            null,
            $currency
        );
    }
}
