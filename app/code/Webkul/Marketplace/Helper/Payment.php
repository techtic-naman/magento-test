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

namespace Webkul\Marketplace\Helper;

use Magento\Framework\Session\SessionManager;
use Webkul\Marketplace\Model\ResourceModel\Product\CollectionFactory as MpProductCollection;
use Webkul\Marketplace\Model\ResourceModel\Saleperpartner\CollectionFactory as SalePerPartnerCollection;
use Webkul\Marketplace\Model\ResourceModel\Saleslist\CollectionFactory as SalesListCollection;
use Webkul\Marketplace\Model\ResourceModel\Orders\CollectionFactory as MpOrdersCollection;
use Magento\Sales\Model\Order\Payment\Transaction;
use Webkul\Marketplace\Model\Product;

/**
 * Webkul Marketplace Helper Payment.
 */
class Payment extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Magento\Customer\Model\Address
     */
    private $modelCustomerAddress;

    /**
     * @var \Magento\Shipping\Model\Config
     */
    private $shippingConfig;

    /**
     * @var SessionManager
     */
    protected $coreSession;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    private $checkoutSession;

    /**
     * @var \Magento\Customer\Model\Session
     */
    private $customerSession;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var MpProductCollection
     */
    private $mpProductCollection;

    /**
     * @var SalePerPartnerCollection
     */
    private $salePerPartnerCollection;

    /**
     * @var \Magento\Sales\Api\OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * @var SalesListCollection
     */
    private $salesListCollection;

    /**
     * @var MpOrdersCollection
     */
    private $mpOrdersCollection;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var \Magento\Sales\Model\Service\InvoiceService
     */
    private $invoiceService;

    /**
     * @var \Magento\Framework\DB\Transaction
     */
    private $dbTransaction;

    /**
     * @var \Magento\Sales\Model\Order\Email\Sender\InvoiceSender
     */
    private $invoiceSender;

    /**
     * @var Transaction\BuilderInterface
     */
    private $transactionBuilder;
    /**
     * @var \Magento\Sales\Api\OrderItemRepositoryInterface
     */
    private $orderItemRepository;
    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;
    /**
     * @var Data
     */
    private $helper;

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Customer\Model\Address $modelCustomerAddress
     * @param \Magento\Shipping\Model\Config $shippingConfig
     * @param SessionManager $coreSession
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Customer\Model\Session $customerSession
     * @param Data $helper
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param MpProductCollection $mpProductCollection
     * @param SalePerPartnerCollection $salePerPartnerCollection
     * @param \Magento\Sales\Api\OrderRepositoryInterface $orderRepository
     * @param SalesListCollection $salesListCollection
     * @param MpOrdersCollection $mpOrdersCollection
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Sales\Model\Service\InvoiceService $invoiceService
     * @param \Magento\Framework\DB\Transaction $dbTransaction
     * @param \Magento\Sales\Model\Order\Email\Sender\InvoiceSender $invoiceSender
     * @param Transaction\BuilderInterface $transactionBuilder
     * @param \Magento\Sales\Api\OrderItemRepositoryInterface $orderItemRepository
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Customer\Model\Address $modelCustomerAddress,
        \Magento\Shipping\Model\Config $shippingConfig,
        SessionManager $coreSession,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Customer\Model\Session $customerSession,
        Data $helper,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        MpProductCollection $mpProductCollection,
        SalePerPartnerCollection $salePerPartnerCollection,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        SalesListCollection $salesListCollection,
        MpOrdersCollection $mpOrdersCollection,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Sales\Model\Service\InvoiceService $invoiceService,
        \Magento\Framework\DB\Transaction $dbTransaction,
        \Magento\Sales\Model\Order\Email\Sender\InvoiceSender $invoiceSender,
        Transaction\BuilderInterface $transactionBuilder,
        \Magento\Sales\Api\OrderItemRepositoryInterface $orderItemRepository
    ) {
        parent::__construct($context);
        $this->modelCustomerAddress = $modelCustomerAddress;
        $this->shippingConfig = $shippingConfig;
        $this->coreSession = $coreSession;
        $this->checkoutSession = $checkoutSession;
        $this->customerSession = $customerSession;
        $this->helper = $helper;
        $this->objectManager = $objectManager;
        $this->mpProductCollection = $mpProductCollection;
        $this->salePerPartnerCollection = $salePerPartnerCollection;
        $this->orderRepository = $orderRepository;
        $this->salesListCollection = $salesListCollection;
        $this->mpOrdersCollection = $mpOrdersCollection;
        $this->storeManager = $storeManager;
        $this->invoiceService = $invoiceService;
        $this->dbTransaction = $dbTransaction;
        $this->invoiceSender = $invoiceSender;
        $this->transactionBuilder = $transactionBuilder;
        $this->logger = $context->getLogger();
        $this->orderItemRepository = $orderItemRepository;
    }

    /**
     * Prepare shipping data
     *
     * @param  array $quote
     * @return array|void
     */
    public function getShippingData($quote)
    {
        try {
            $allmethods = [];
            $shippingData = [];
            $newvar = '';

            $customerAddressData = $this->getCustomerAddressData($quote);

            $shippingMethod = $customerAddressData['shipping_method'];
            $customerAddressId = $customerAddressData['customer_address_id'];
            $shippingTaxAmount = $customerAddressData['shipping_tax_amount'];
            $shippingAmount = $customerAddressData['shipping_amount'];

            $customerAddress = $this->modelCustomerAddress->load($customerAddressId);
            $customerName = $customerAddress['firstname'].' '.$customerAddress['lastname'];

            //Guest User
            if (!$customerAddressId || $customerAddressId == null) {
                $customerName = $quote->getBillingAddress()->getFirstname()
                . ' '
                . $quote->getBillingAddress()->getLastname();

                $customerAddress = $quote->getBillingAddress();
            }

            $methods = $this->shippingConfig->getActiveCarriers();

            foreach ($methods as $_code => $_method) {
                array_push($allmethods, $_code);
            }

            if ($shippingMethod == 'mpmultishipping_mpmultishipping') {
                $newvar = 'webkul';
                $shippinginfo = $this->coreSession->getData('selected_shipping');
                foreach ($shippinginfo as $val) {
                    $taxAmount = $this->calculateTaxByPercent($val['price'], $quote);
                    $shippingData[] = [
                        'seller' => $val['sellerid'],
                        'amount' => $val['price'] + $taxAmount,
                        'method' => $val['method']
                    ];
                }
            } else {
                $shipmethod = explode('_', $shippingMethod, 2);
                $shippinginfo = $this->checkoutSession->getShippingInfo();

                if (empty($shippinginfo) || $shippinginfo=="" || $shippinginfo==null) {
                    $shippinginfo = $this->coreSession->getShippingInfo();
                }

                if (in_array($shipmethod[0], $allmethods)
                    && !empty($shippinginfo[$shipmethod[0]])
                ) {
                    $shippingData = $this->getShippingInfoShippingData(
                        $shippinginfo,
                        $shipmethod,
                        $shippingData
                    );
                }
            }

            return [
                'newvar' => $newvar,
                'shippingTaxAmount' => $shippingTaxAmount,
                'shippingAmount' => $shippingAmount,
                'shipinf' => $shippingData,
                'customName' => $customerName,
                'customerAddress' => $customerAddress,
                'shipping_method' => $shippingMethod
            ];
        } catch (\Exception $e) {
            $this->logData("Helper_Payment getShippingData : ".$e->getMessage());
        }
    }

    /**
     * Get shipping data for o index shipping method
     *
     * @param array $shippinginfo
     * @param array $shipmethod
     * @param array $shippingData
     * @return array
     */
    public function getShippingInfoShippingData($shippinginfo, $shipmethod, $shippingData)
    {
        $quote = false;
        foreach ($shippinginfo[$shipmethod[0]] as $key) {
            foreach ($key['submethod'] as $k => $v) {
                if ($k == $shipmethod[1]) {
                    $taxAmount = $this->calculateTaxByPercent($v['cost'], $quote);
                    $shippingData[] = [
                        'seller' => $key['seller_id'],
                        'amount' => $v['cost'] + $taxAmount,
                        'method' => $shipmethod[1]
                    ];
                }
            }
        }

        return $shippingData;
    }

    /**
     * Prepare data from customer address
     *
     * @param  array $quote contains current quote
     * @return array|void
     */
    public function getCustomerAddressData($quote)
    {
        try {
            $shippingTaxAmount = 0;
            $shippingAmount = 0;
            $customerAddressId = 0;

            if (!empty($quote->getShippingAddress())) {
                $shippingMethod = $quote->getShippingAddress()->getShippingMethod();

                $shippingData = $this->calculateShippingTax(
                    $quote->getShippingAddress()
                );
                $shippingTaxAmount = $shippingData['shippingTaxAmount'];
                $shippingAmount = $shippingData['shippingAmount'];

                $customerAddressId = $quote->getShippingAddress()
                    ->getCustomerAddressId();
            } else {
                $shippingMethod = '';
                $customerAddressId = $quote->getBillingAddress()
                    ->getCustomerAddressId();
            }
            if ($customerAddressId == null) {
                $customerAddressId = $quote->getBillingAddress()
                    ->getCustomerAddressId();
            }
            return [
                'shipping_method' => $shippingMethod,
                'customer_address_id' => $customerAddressId,
                'shipping_tax_amount' => $shippingTaxAmount,
                'shipping_amount' => $shippingAmount
            ];
        } catch (\Exception $e) {
            $this->logData("Helper_Payment getCustomerAddressData : ".$e->getMessage());
        }
    }

    /**
     * Calculate tax from a tax percentage
     *
     * @param  float $amount contains an amount on which tax is calculated
     * @param  array $quote contains current quote
     * @return void|int|float
     */
    public function calculateTaxByPercent($amount, $quote)
    {
        try {
            $shippingTaxConfig = $this->getShippingTaxCalculationConfig();

            if ($shippingTaxConfig == 0) {
                $percent = $this->getTaxPercent($quote);
                if ($percent !== 0 && $amount !== 0) {
                    $taxAmount = ( $amount * $percent ) / 100 ;
                    return round($taxAmount, 4);
                } else {
                    return 0;
                }
            } else {
                return 0;
            }
        } catch (\Exception $e) {
            $this->logData("Helper_Payment calculateTaxByPercent : ".$e->getMessage());
        }
    }

    /**
     * Get tax percentage from applied taxes
     *
     * @param  array $quote contains current quote
     * @return int|float
     */
    public function getTaxPercent($quote)
    {
        try {
            $appliedTaxes = $quote->getShippingAddress()->getAppliedTaxes();
            if (!empty($appliedTaxes)) {
                foreach ($appliedTaxes as $type => $value) {
                    if ($type == "shipping") {
                        return $value['percent'];
                    }
                }
            } else {
                return 0;
            }
        } catch (\Exception $e) {
            $this->logData("Helper_Payment getTaxPercent : ".$e->getMessage());
            return 0;
        }
        return 0;
    }

    /**
     * Get shipping tax amount and shipping amount from shipping address
     *
     * @param  array $shippingAddress contains current shipping address
     * @return array|void
     */
    public function calculateShippingTax($shippingAddress)
    {
        try {
            $shippingTaxAmount = $shippingAddress->getData(
                'base_shipping_tax_amount'
            );

            $shippingAmount = $shippingAddress->getData(
                'base_shipping_amount'
            );

            $shippingAmountInclTax = $shippingAddress->getData(
                'base_shipping_incl_tax'
            );

            if ($shippingAmount < $shippingAmountInclTax && $shippingTaxAmount!==0) {
                $shippingTaxAmount = 0;
                $shippingAmount = $shippingAddress->getData(
                    'base_shipping_incl_tax'
                );
            }

            return [
                'shippingTaxAmount' => $shippingTaxAmount,
                'shippingAmount' => $shippingAmount
            ];
        } catch (\Exception $e) {
            $this->logData("Helper_Payment calculateShippingTax : ".$e->getMessage());
        }
    }

    /**
     * Get admin config of shipping includes tax or not
     *
     * @return boolean|void
     */
    public function getShippingTaxCalculationConfig()
    {
        try {
            return $this->scopeConfig->getValue(
                'tax/calculation/shipping_includes_tax',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );
        } catch (\Exception $e) {
            $this->logData("Helper_Payment getShippingTaxCalculationConfig : ".$e->getMessage());
        }
    }

    /**
     * Calculate total discount amount of a bundle product
     *
     * @param int $itemId contains order item Id
     * @param array $order contains order
     * @return int|float
     */
    public function calculateBundleProductDiscount($itemId, $order)
    {
        $childDiscount = 0;
        try {
            foreach ($order->getAllItems() as $item) {
                if ($item->getParentItem() && $item->getParentItemId()==$itemId) {
                    $childDiscount += $item->getBaseDiscountAmount();
                }
            }
        } catch (\Exception $e) {
            $this->logData("Helper_Payment calculateBundleProductDiscount : ".$e->getMessage());
        }
        return $childDiscount;
    }

    /**
     * Prepare commission data for admin
     *
     * @param  array $item contains order item
     * @return array|void
     */
    public function getCommissionData($item)
    {
        try {
            $itemId = $item->getProductId();
            $tempcoms = 0;
            $commissionDetail = [];
            $mpAssignProductId = 0;

            $advanceCommissionRule = $this->customerSession->getData(
                'advancecommissionrule'
            );

            $commType = $this->helper->getCommissionType();
            $configCommissionRate = $this->helper->getConfigCommissionRate();
            $rowTotal = $item->getBaseRowTotal();

            $infoBuyRequest = $item->getProductOptionByCode('info_buyRequest');

            $mpAssignProductId = 0;
            if (!empty($infoBuyRequest['mpassignproduct_id'])) {
                $mpAssignProductId = $infoBuyRequest['mpassignproduct_id'];
            }
            if ($mpAssignProductId) {
                $mpassignModel = $this->objectManager->create(
                    \Webkul\MpAssignProduct\Model\Items::class
                )->load($mpAssignProductId);
                $sellerId = $mpassignModel->getSellerId();
            } elseif (is_array($infoBuyRequest) && array_key_exists('seller_id', $infoBuyRequest)) {
                $sellerId = $infoBuyRequest['seller_id'];
            } else {
                $sellerId = '';
            }

            if ($sellerId == '') {
                $seller = $this->mpProductCollection->create()
                    ->addFieldToFilter(
                        'mageproduct_id',
                        $itemId
                    );

                if (!$this->helper->getUseCommissionRule()) {
                    $this->_eventManager->dispatch(
                        'mp_advance_commission',
                        ['id' => $itemId]
                    );
                    $advancecommission = $this->customerSession->getData(
                        'commission'
                    );
                    if ($advancecommission != '') {
                        $tempcoms = $this->calculateCommission($commType, $advancecommission, $rowTotal);
                        if ($tempcoms > $rowTotal) {
                            $tempcoms = $rowTotal * $configCommissionRate / 100;
                        }
                        foreach ($seller as $usr) {
                            $commissionDetail['id'] = $usr->getSellerId();
                        }
                    }
                } else {
                    if (!empty($advanceCommissionRule)) {
                        $tempcoms = $this->calculateCommission(
                            $advanceCommissionRule[$item->getId()]['type'],
                            $advanceCommissionRule[$item->getId()]['amount'],
                            $rowTotal
                        );
                        foreach ($seller as $usr) {
                            $commissionDetail['id'] = $usr->getSellerId();
                        }
                    }
                }
            }

            return [
                'tempcoms' => $tempcoms,
                'commissionDetail' => $commissionDetail,
                'row_total' => $rowTotal,
                'product_id' => $itemId
            ];
        } catch (\Exception $e) {
            $this->logData("Helper_Payment getCommissionData : ".$e->getMessage());
        }
    }

    /**
     * Calculate commission
     *
     * @param  string $type type is fixed or in percentage
     * @param  float $amount parameter to calculate commission
     * @param  float $rowTotal on which commision is calculated
     * @return int|float
     */
    public function calculateCommission($type, $amount, $rowTotal)
    {
        try {
            if ($type == 'fixed') {
                $tempcoms = $amount;
            } else {
                $tempcoms = (
                    $rowTotal * $amount
                ) / 100;
            }
            return $tempcoms;
        } catch (\Exception $e) {
            $this->logData("Helper_Payment calculateCommission : ".$e->getMessage());
        }
    }

    /**
     * Prepare seller wise commission data
     *
     * @param  int $productId product Id
     * @return array
     */
    public function getSellerDetail($productId = '')
    {
        $data = [
            'id' => 0,
            'commission' => 0
        ];
        try {
            $sell = $this->mpProductCollection->create()
                    ->addFieldToFilter('mageproduct_id', $productId);
            if ($sell->getSize()) {
                foreach ($sell as $seller) {
                    $data['id'] = $seller->getSellerId();
                    $commissionRate = $this->salePerPartnerCollection->create()
                        ->addFieldToFilter('seller_id', $seller->getSellerId())
                        ->addFieldToFilter(
                            'commission_status',
                            1
                        )
                        ->addFieldToSelect(
                            'commission_rate'
                        )
                        ->getFirstItem()
                        ->getCommissionRate();
                    if ($commissionRate !== null) {
                        $data['commission'] = $commissionRate;
                    } else {
                        $data['commission'] = $this->helper->getConfigCommissionRate();
                    }
                }
            }
        } catch (\Exception $e) {
            $this->logData("Helper_Payment getSellerDetail : ".$e->getMessage());
        }
        return $data;
    }

    /**
     * Get real seller id if seller price comparison module installed
     *
     * @param  array $item order item
     * @return int seller id
     */
    public function getRealSellerId($item)
    {
        $sellerId = '';
        try {
            $productId = $item->getProductId();
            $mpAssignProductId = 0;

            $infoBuyRequest = $item->getProductOptionByCode('info_buyRequest');

            $mpAssignProductId = 0;
            if (isset($infoBuyRequest['mpassignproduct_id'])) {
                $mpAssignProductId = $infoBuyRequest['mpassignproduct_id'];
            }
            if ($mpAssignProductId) {
                $mpassignModel = $this->objectManager->create(
                    \Webkul\MpAssignProduct\Model\Items::class
                )->load($mpAssignProductId);
                $sellerId = $mpassignModel->getSellerId();
            } elseif (is_array($infoBuyRequest) && array_key_exists('seller_id', $infoBuyRequest)) {
                $sellerId = $infoBuyRequest['seller_id'];
            }

            if ($sellerId == '') {
                $seller = $this->mpProductCollection->create()
                    ->addFieldToFilter(
                        'mageproduct_id',
                        $productId
                    );
                foreach ($seller as $usr) {
                    $sellerId = $usr->getSellerId();
                    break;
                }
            }
        } catch (\Exception $e) {
            $this->logData("Helper_Payment getRealSellerId : ".$e->getMessage());
        }
        return $sellerId;
    }

    /**
     * Get seller coupon amount if seller coupon module installed
     *
     * @param  int $sellerId seller Id
     * @return int|float
     */
    public function getSellerCouponAmount($sellerId)
    {
        $amount = 0;
        try {
            if ($this->helper->isSellerCouponModuleInstalled() && $sellerId !== '') {
                $info = $this->checkoutSession->getCouponInfo();
                if (!empty($info[$sellerId]['seller_id'])
                    && $info[$sellerId]['seller_id'] == $sellerId
                    && !empty($info[$sellerId]['amount'])
                ) {
                    $amount = $info[$sellerId]['amount'];
                }
            }
        } catch (\Exception $e) {
            $this->logData("Helper_Payment getSellerCouponAmount : ".$e->getMessage());
        }
        return $amount;
    }

    /**
     * Get seller credit point amount if seller credit module installed
     *
     * @param  int $sellerId seller Id
     * @return int|float
     */
    public function getCreditPoints($sellerId)
    {
        $amount = 0;
        try {
            if ($this->_moduleManager->isEnabled('Webkul_Mpsellercredits') && $sellerId !== '') {
                $creditInfo = $this->coreSession->getCreditInfo();
                if (!empty($creditInfo[$sellerId]['amount'])) {
                    $amount = $creditInfo[$sellerId]['amount'];
                }
            }
        } catch (\Exception $e) {
            $this->logData("Helper_Payment getCreditPoints : ".$e->getMessage());
        }
        return $amount;
    }

    /**
     * Calculate adjustment negative amount
     *
     * @param  float $adjustmentNegative Adjustment Fee for credit memo
     * @param  float $adjustmentPositive Adjustment Refund for credit memo
     * @return int|float
     */
    public function getAdjustmentNegative($adjustmentNegative, $adjustmentPositive)
    {
        try {
            if ($adjustmentNegative > $adjustmentPositive) {
                $adjustmentNegative -= $adjustmentPositive;
            } else {
                $adjustmentNegative = 0;
            }
        } catch (\Exception $e) {
            $this->logData("Helper_Payment getAdjustmentNegative : ".$e->getMessage());
        }
        return $adjustmentNegative;
    }

    /**
     * Calculation of the refund item ordered price
     *
     * @param  int $productId Product Id
     * @param  int $orderId Order Id
     * @return int|float
     */
    public function getItemPrice($productId, $orderId)
    {
        try {
            $order = $this->orderRepository->get($orderId);
            foreach ($order->getAllItems() as $item) {
                if ($item->getProductId() == $productId) {
                    return $item->getBasePrice();
                }
            }
        } catch (\Exception $e) {
            $this->logData("Helper_Payment getItemPrice : ".$e->getMessage());
        }
        return 0;
    }

    /**
     * Prepare data for credit memo
     *
     * @param  array $refundData request parameters for refund
     * @param  int $orderid Order Id
     * @return array
     */
    public function getCreditmemoItemData(
        $refundData,
        $orderid
    ) {
        $creditmemoItemsIds = [];
        $creditmemoItemsQty = [];
        $creditmemoItemsPrice = [];
        $creditmemoCommissionRateArr = [];

        try {
            foreach ($refundData['creditmemo']['items'] as $key => $value) {
                $productId = '';
                $sellerProducts = $this->salesListCollection->create()
                    ->addFieldToFilter(
                        'order_item_id',
                        $key
                    )->addFieldToFilter(
                        'order_id',
                        $orderid
                    );
                foreach ($sellerProducts as $sellerProduct) {
                    $productId = $sellerProduct['mageproduct_id'];

                    if ($productId) {
                        $creditmemoItemsIds[$key] = $productId;
                        $creditmemoItemsQty[$key] = $value['qty'];
                        $creditmemoItemsPrice[$key] = $this->getItemPrice(
                            $productId,
                            $orderid
                        ) * $value['qty'];
                        $creditmemoCommissionRateArr[$key] = $sellerProduct->getData();
                    }
                }
            }
        } catch (\Exception $e) {
            $this->logData("Helper_Payment getCreditmemoItemData : ".$e->getMessage());
        }
        arsort($creditmemoItemsPrice);
        return [
            'creditmemoItemsIds'=>$creditmemoItemsIds,
            'creditmemoItemsQty'=>$creditmemoItemsQty,
            'creditmemoItemsPrice'=>$creditmemoItemsPrice,
            'creditmemoCommissionRateArr'=>$creditmemoCommissionRateArr,
        ];
    }

    /**
     * Prepare data to refund to seller and admin
     *
     * @param  array $creditmemoItemsData credit memo item data
     * @param  float $adjustmentNegative Adjustment Fee for credit memo
     * @param  int $orderId Order Id
     * @return array
     */
    public function getAdminAmountAndSellerData(
        $creditmemoItemsData,
        $adjustmentNegative,
        $orderId
    ) {
        $sellerArr = [];
        $adminAmountToRefund = 0;
        try {
            $creditmemoItemsQty = $creditmemoItemsData['creditmemoItemsQty'];
            $creditmemoItemsPrice = $creditmemoItemsData['creditmemoItemsPrice'];
            $creditmemoCommissionRateArr = $creditmemoItemsData['creditmemoCommissionRateArr'];

            foreach ($creditmemoItemsPrice as $key => $item) {
                $refundedQty = $creditmemoItemsQty[$key];
                $refundedPrice = $creditmemoItemsPrice[$key];
                $sellerProduct = $creditmemoCommissionRateArr[$key];
                if ($adjustmentNegative * 1) {
                    if ($adjustmentNegative >= $sellerProduct['total_amount']) {
                        $adjustmentNegative -= $sellerProduct['total_amount'];
                        $refundedPrice = 0;
                    } else {
                        $refundedPrice -=  $adjustmentNegative;
                        $adjustmentNegative = 0;
                    }
                }
                if (!($sellerProduct['total_amount'] * 1)) {
                    $sellerProduct['total_amount'] = 1;
                }
                if ($sellerProduct['total_commission'] * 1) {
                    $commissionPercentage = (
                        $sellerProduct['total_commission'] * 100
                    ) / $sellerProduct['total_amount'];
                } else {
                    $commissionPercentage = 0;
                }
                $updatedCommission = ($refundedPrice * $commissionPercentage) / 100;
                $updatedSellerAmount = $refundedPrice - $updatedCommission;

                $taxAmount = $this->getFinalTaxAmount($refundedQty, $sellerProduct, $orderId);
                $updatedSellerAmount += $taxAmount;

                $couponAmountData = $this->getSellerDiscountAmount($refundedQty, $sellerProduct);
                $updatedSellerAmount -= $couponAmountData['discount'];

                if (!isset($sellerArr[$sellerProduct['seller_id']]['seller_refund'])) {
                    $sellerArr[$sellerProduct['seller_id']]['seller_refund'] = 0;
                }

                if (!isset($sellerArr[$sellerProduct['seller_id']]['updated_commission'])) {
                    $sellerArr[$sellerProduct['seller_id']]['updated_commission'] = 0;
                }

                $sellerArr[$sellerProduct['seller_id']]['seller_refund'] += $updatedSellerAmount;

                $sellerArr[$sellerProduct['seller_id']]['updated_commission'] += $updatedCommission;
                $adminAmountToRefund += $updatedCommission;

                if ($couponAmountData['remaining_coupon_amount']) {
                    $sellerProduct->setAppliedCouponAmount($couponAmountData['remaining_coupon_amount']);
                    $sellerProduct->save();
                }
            }
        } catch (\Exception $e) {
            $this->logData("Helper_Payment getAdminAmountAndSellerData : ".$e->getMessage());
        }
        return [
            'sellerArr' => $sellerArr,
            'adminAmountToRefund' => $adminAmountToRefund
        ];
    }

    /**
     * Calculate refund Tax Amount according to refunded quantity of item
     *
     * @param  int $refundedQty item quantity to refund
     * @param  array $sellerProduct seller's product's table data
     * @param  int $orderId Order Id
     * @return int|float
     */
    public function getFinalTaxAmount(
        $refundedQty,
        $sellerProduct,
        $orderId
    ) {
        $taxAmount = 0;
        $orderId = 1;
        try {
            $orderItem = $this->orderItemRepository->get($sellerProduct['order_item_id']);
            if ($refundedQty) {
                if ((int)$sellerProduct['magequantity'] != ''
                    || (int)$sellerProduct['magequantity'] != 0
                ) {
                    $taxAmt = $sellerProduct['total_tax'];

                    if ($orderItem->getBaseTaxAmount() > $taxAmt) {
                        $taxAmt = $orderItem->getBaseTaxAmount() - $taxAmt;
                        if ($taxAmt > 0 && $taxAmt < 1) {
                            $taxAmt = $orderItem->getBaseTaxAmount();
                        } else {
                            $taxAmt = $sellerProduct['total_tax'];
                        }
                    }
                    $taxAmount = (
                        $taxAmt / $sellerProduct['magequantity']
                    ) * $refundedQty;
                }
            }

            if (!$this->helper->getConfigTaxManage()) {
                $taxAmount = 0;
            }
        } catch (\Exception $e) {
            $this->logData("Helper_Payment getFinalTaxAmount : ".$e->getMessage());
        }
        return $taxAmount;
    }

    /**
     * Calculate seller discount amount according to refunded quantity of item
     *
     * @param  int $refundedQty item quantity to refund
     * @param  array $sellerProduct seller's product's table data
     * @return array
     */
    public function getSellerDiscountAmount(
        $refundedQty,
        $sellerProduct
    ) {
        $discount = 0;
        $remainAppliedCouponAmount = false;
        try {
            if ($refundedQty) {
                if ((int)$sellerProduct['magequantity'] != ''
                    || (int)$sellerProduct['magequantity'] != 0
                ) {
                    $discount = (
                        $sellerProduct['applied_coupon_amount'] / $sellerProduct['magequantity']
                    ) * $refundedQty;
                    $remainAppliedCouponAmount = $sellerProduct['applied_coupon_amount'] - $discount;
                }
            }
        } catch (\Exception $e) {
            $this->logData("Helper_Payment getSellerDiscountAmount : ".$e->getMessage());
        }
        return [
            'discount' => $discount,
            'remaining_coupon_amount' => $remainAppliedCouponAmount
        ];
    }

    /**
     * Calculate admin shipping amount to refund
     *
     * @param float $shippingCharges
     * @param array $orderData
     * @param array $refundData
     * @return int|float
     */
    public function getAdminShippingAmount(
        $shippingCharges,
        $orderData,
        $refundData
    ) {
        $adminShipping = 0;
        try {
            if ($shippingCharges == 0
                && (($orderData['shipping_amount'] * 1) >= ($orderData['shipping_refunded'] * 1))
            ) {
                $adminShipping = $refundData['creditmemo']['shipping_amount'];
                $shippingTaxAmount = $orderData['shipping_tax_amount'];
                $orderShippingTaxRefunded = $orderData['shipping_tax_refunded'];
                if (($shippingTaxAmount * 1) >= ($orderShippingTaxRefunded * 1) && $orderData['shipping_amount']>0) {
                    $shipTaxPercentage = ($shippingTaxAmount * 100) / $orderData['shipping_amount'];
                    $adminShipping += (
                        $refundData['creditmemo']['shipping_amount'] * $shipTaxPercentage / 100
                    );
                }
            }
        } catch (\Exception $e) {
            $this->logData("Helper_Payment getAdminShippingAmount : ".$e->getMessage());
        }
        return $adminShipping;
    }

    /**
     * Update shipping amount to refund
     *
     * @param  int $orderId Order Id
     * @param  int $sellerId Seller Id
     * @param  array $refundData request parameters for refund
     * @return array
     */
    public function updateShippingRefundData(
        $orderId,
        $sellerId,
        $refundData
    ) {
        $shippingCharges = 0;
        try {
            $trackingcoll = $this->mpOrdersCollection->create()
                ->addFieldToFilter('order_id', $orderId)
                ->addFieldToFilter('seller_id', $sellerId);
            foreach ($trackingcoll as $tracking) {
                $shippingCharges = $tracking->getShippingCharges();
                if ($shippingCharges * 1) {
                    if ($shippingCharges > $refundData['creditmemo']['shipping_amount']) {
                        $shippingCharges = $refundData['creditmemo']['shipping_amount'];
                        $refundData['creditmemo']['shipping_amount'] = 0;
                    } else {
                        $refundData['creditmemo']['shipping_amount'] -= $shippingCharges;
                    }
                }
            }
        } catch (\Exception $e) {
            $this->logData("Helper_Payment updateShippingRefundData : ".$e->getMessage());
        }
        return [
            'refund_data' => $refundData,
            'shipping_charges' => $shippingCharges
        ];
    }

    /**
     * Convert an amount from base currency to current currency
     *
     * @param  float $amount amount to convert
     * @return int|float
     */
    public function convertFromBaseToCurrentCurrency($amount)
    {
        try {
            $amount = $this->storeManager->getStore()->getBaseCurrency()->convert(
                $amount,
                $this->storeManager->getStore()->getCurrentCurrency()
            );
        } catch (\Exception $e) {
            $this->logData("Helper_Payment convertFromBaseToCurrentCurrency : ".$e->getMessage());
        }
        return $amount;
    }

    /**
     * Revert Seller Payment to sale per partner table
     *
     * @param  array $order order
     * @param  int $sellerid seller Id
     * @return void
     */
    public function revertSellerPayment($order, $sellerid)
    {
        try {
            $lastOrderId = $order->getId();
            $actparterprocost = 0;
            $totalamount = 0;
            /*
            * Calculate cod and shipping charges if applied
            */
            $codCharges = 0;
            $shippingCharges = 0;
            $sellerOrder = $this->mpOrdersCollection->create()
                ->addFieldToFilter('seller_id', $sellerid)
                ->addFieldToFilter('order_id', $lastOrderId);

            foreach ($sellerOrder as $info) {
                $codCharges = $info->getCodCharges();
                $shippingCharges = $info->getShippingCharges();
            }

            $collection = $this->salesListCollection->create()
                ->addFieldToFilter('seller_id', $sellerid)
                ->addFieldToFilter('order_id', $lastOrderId)
                ->addFieldToFilter('paid_status', 0)
                ->addFieldToFilter('cpprostatus', 0);

            foreach ($collection as $row) {
                $taxAmount = $row['total_tax'];
                $vendorTaxAmount = 0;
                if ($this->helper->getConfigTaxManage()) {
                    $vendorTaxAmount = $taxAmount;
                }
                $actparterprocost += $row->getActualSellerAmount() +
                $vendorTaxAmount +
                $codCharges +
                $shippingCharges;

                $totalamount += $row->getTotalAmount() +
                $taxAmount +
                $codCharges +
                $shippingCharges;

                $codCharges = 0;
                $shippingCharges = 0;
                $sellerId = $row->getSellerId();
            }
            if ($actparterprocost) {
                $collectionverifyread = $this->salePerPartnerCollection->create()
                    ->addFieldToFilter('seller_id', $sellerId);

                if ($collectionverifyread->getSize() >= 1) {
                    foreach ($collectionverifyread as $verifyrow) {
                        if ($verifyrow->getAmountRemain() >= $actparterprocost) {
                            $totalremain = $verifyrow->getAmountRemain() - $actparterprocost;
                        } else {
                            $totalremain = 0;
                        }
                        $totalcommission = $verifyrow->getTotalCommission() - (
                            $totalamount - $actparterprocost
                        );
                        $totalsale = $verifyrow->getTotalSale() - $totalamount;
                        $verifyrow->setTotalSale($totalsale);
                        $verifyrow->setAmountRemain($totalremain);
                        $verifyrow->setTotalCommission($totalcommission);
                        $verifyrow->save();
                    }
                }
            }
        } catch (\Exception $e) {
            $this->logData("Helper_Payment revertSellerPayment : ".$e->getMessage());
        }
    }

    /**
     * Get cash on delivery charges if applied
     *
     * @param  string $paymentCode payment method
     * @param  array $tracking Mp Order data
     * @param  float $codCharges charges for cash on delivery
     * @return int|float
     */
    public function getCodChargesIfApplied($paymentCode, $tracking, $codCharges)
    {
        try {
            if ($paymentCode == 'mpcashondelivery') {
                $codCharges += $tracking->getCodCharges();
            }
        } catch (\Exception $e) {
            $this->logData("Helper_Payment getCodChargesIfApplied : ".$e->getMessage());
        }
        return $codCharges;
    }

    /**
     * Get Order item array data.
     *
     * @param  object $order Order
     * @param  array $items array of ordered item ids
     * @return array|void
     */
    public function getItemQtys($order, $items)
    {
        try {
            $data = [];
            $subtotal = 0;
            $baseSubtotal = 0;
            foreach ($order->getAllItems() as $item) {
                if (in_array($item->getItemId(), $items)) {
                    $data[$item->getItemId()] = (int)(
                        $item->getQtyOrdered() - $item->getQtyInvoiced()
                    );

                    $_item = $item;

                    if ($_item->getParentItem()) {
                        continue;
                    }

                    if ($_item->getProductType() == Product::PRODUCT_TYPE_BUNDLE) {
                        $data = $this->getBundleItemData($_item, $data);
                    }
                    $subtotal += $_item->getRowTotal();
                    $baseSubtotal += $_item->getBaseRowTotal();
                } else {
                    if (!$item->getParentItemId()) {
                        $data[$item->getItemId()] = 0;
                    }
                }
            }

            return [
                'data' => $data,
                'subtotal' => $subtotal,
                'baseSubtotal' => $baseSubtotal
            ];
        } catch (\Exception $e) {
            $this->logData("Helper_Payment getItemQtys : ".$e->getMessage());
        }
    }

    /**
     * Get Bundle Item Data
     *
     * @param \Magento\Sales\Model\Order\Item $_item
     * @param array $data
     * @return array
     */
    public function getBundleItemData($_item, $data)
    {
        // for bundle product
        $bundleitems = array_merge(
            [$_item],
            $_item->getChildrenItems()
        );
        foreach ($bundleitems as $_bundleitem) {
            if ($_bundleitem->getParentItem()) {
                $data[$_bundleitem->getItemId()] = (int)(
                    $_bundleitem->getQtyOrdered() - $_item->getQtyInvoiced()
                );
            }
        }

        return $data;
    }

    /**
     * Calculate total tax according to item quantities
     *
     * @param  array $itemsarray array contains item ids with quantities to invoice
     * @param  float $tax tax for single quantity of an item
     * @return int|float
     */
    public function calculateTax(
        $itemsarray,
        $tax
    ) {
        $newTax = 0;
        try {
            foreach ($itemsarray['data'] as $value) {
                if ((int)$value!==0
                    && $value!==""
                    && $newTax!==null
                ) {
                    $newTax += $tax * (int)$value;
                }
            }
        } catch (\Exception $e) {
            $this->logData("Helper_Payment calculateTax : ".$e->getMessage());
        }
        return $newTax;
    }

    /**
     * Create remaining order shipping amount invoice.
     *
     * @param \Magento\Sales\Model\Order $order
     * @param int $transactionId
     * @param float $adminBaseShippingAmount
     * @return void
     */
    public function createShippingInvoice($order, $transactionId, $adminBaseShippingAmount)
    {
        try {
            if ($order->getBaseTotalDue()) {
                $baseShippingAmount = $order->getBaseTotalDue();
                $invoice = $this->invoiceService->prepareInvoice($order);
                $invoice->setTransactionId($transactionId);
                $invoice->setRequestedCaptureCase('online');
                if ($adminBaseShippingAmount > 0) {
                    $invoice->setBaseShippingInclTax($adminBaseShippingAmount);
                    $invoice->setBaseShippingAmount($adminBaseShippingAmount);
                }

                $invoice->setBaseGrandTotal($baseShippingAmount);
                $invoice->register();
                $invoice->save();
                $invoice->getOrder()->setIsInProcess(true);

                $transactionSave = $this->dbTransaction->addObject(
                    $invoice
                )->addObject(
                    $invoice->getOrder()
                );
                $transactionSave->save();

                $this->invoiceSender->send($invoice);

                $orderId = $order->getId();
                $order = $this->orderRepository->get($orderId);
                $order->addStatusHistoryComment(
                    __('Notified customer about invoice #%1.', $invoice->getId())
                );
                $order->setIsCustomerNotified(true);
                $order->setState('processing');
                $order->setStatus('processing');
                $order->save();
            }
        } catch (\Exception $e) {
            $this->logData("Helper_Payment createShippingInvoice : ".$e->getMessage());
        }
    }

    /**
     * Create order invoice.
     *
     * @param  object $order
     * @param  int $transactionId
     * @return void
     */
    public function createOrderInvoice($order, $transactionId)
    {
        try {
            if ($order->canInvoice()) {
                $invoice = $this->invoiceService->prepareInvoice($order);
                $invoice->setTransactionId($transactionId);
                $invoice->setRequestedCaptureCase('online');
                $invoice->register();
                $invoice->save();
                $transactionSave = $this->dbTransaction->addObject(
                    $invoice
                )->addObject(
                    $invoice->getOrder()
                );
                $transactionSave->save();
                $this->invoiceSender->send($invoice);
                //send notification code
                $orderId = $order->getId();
                $order = $this->orderRepository->get($orderId);
                $order->addStatusHistoryComment(
                    __('Notified customer about invoice #%1.', $invoice->getId())
                );
                $order->setIsCustomerNotified(true);
                $order->setState('processing');
                $order->setStatus('processing');
                $order->save();
            }
        } catch (\Exception $e) {
            $this->logData("Helper_Payment createOrderInvoice : ".$e->getMessage());
        }
    }

    /**
     * Prepare data to generate invoice seller wise
     *
     * @param  int $orderId Order Id
     * @return array|void
     */
    public function getSellerOrderData($orderId = '')
    {
        try {
            $flag = 0;
            $idsToCreateInvoice = [];
            $sellerIdsData = [];

            $ordercollection = $this->salesListCollection->create()
                ->addFieldToFilter('order_id', $orderId)
                ->addFieldToFilter('cpprostatus', 0);

            foreach ($ordercollection as $orderitem) {
                $flag = 1;
                array_push($sellerIdsData, $orderitem->getSellerId());
                if (isset($idsToCreateInvoice[$orderitem->getSellerId()])
                    && $idsToCreateInvoice[$orderitem->getSellerId()]
                ) {
                    $idsToCreateInvoice[$orderitem->getSellerId()] += $orderitem->getActualSellerAmount();
                } else {
                    $idsToCreateInvoice[$orderitem->getSellerId()] = $orderitem->getActualSellerAmount();
                }
            }

            return [
                'seller_ids_data' => $sellerIdsData,
                'seller_amount_data' => $idsToCreateInvoice,
                'flag' => $flag
            ];
        } catch (\Exception $e) {
            $this->logData("Helper_Payment getSellerOrderData : ".$e->getMessage());
        }
    }

    /**
     * Create seller shipment invoice
     *
     * @param \Magento\Sales\Model\Order $order
     * @param array $itemsarray
     * @param string $transactionId
     * @param string $paymentCode
     * @param float $shippingAmount
     * @param int $sellerId
     * @param float $codCharges
     * @param float $tax
     * @param float $sellerCouponAmount
     * @return void
     */
    public function createSellerOrderInvoice(
        $order,
        $itemsarray,
        $transactionId,
        $paymentCode,
        $shippingAmount,
        $sellerId,
        $codCharges,
        $tax,
        $sellerCouponAmount
    ) {
        try {
            if ($order->canInvoice()) {
                $orderId = $order->getId();
                $newTax = 0;
                $subtotal = $itemsarray['subtotal'];
                $baseSubtotal = $itemsarray['baseSubtotal'];

                $baseShippingTaxAmount = $order->getBaseShippingTaxAmount();
                $totalQtyOrdered = $order->getTotalQtyOrdered();
                if ($baseShippingTaxAmount !== null
                    && $baseShippingTaxAmount !== ''
                ) {
                    $newtax = $baseShippingTaxAmount / $totalQtyOrdered;
                    $newTax = $this->calculateTax(
                        $itemsarray,
                        $newtax
                    );
                }

                $taxAmount = $tax+$newTax;

                $orderShippingAmount = $shippingAmount;
                $orderCouponAmount = $sellerCouponAmount;
                $ordererdTaxAmount = $taxAmount;
                $ordererdCodCharges = $codCharges;

                if ($order->getOrderCurrencyCode() !== $order->getBaseCurrencyCode()) {
                    $orderShippingAmount = $this->getCurrentCurrencyAmountbyorder(
                        $order,
                        $shippingAmount
                    );
                    $ordererdTaxAmount = $this->getCurrentCurrencyAmountbyorder(
                        $order,
                        $tax+$newTax
                    );
                    $ordererdCodCharges = $this->getCurrentCurrencyAmountbyorder(
                        $order,
                        $codCharges
                    );
                    if ($sellerCouponAmount > 0) {
                        $orderCouponAmount = $this->getCurrentCurrencyAmountbyorder(
                            $order,
                            $sellerCouponAmount
                        );
                    }
                }

                $grandTotal = $subtotal+$orderShippingAmount+$ordererdCodCharges+$ordererdTaxAmount-$orderCouponAmount;
                $grandTotal = round($grandTotal, 4);

                $baseGrandTotal = $baseSubtotal+$shippingAmount+$codCharges+$taxAmount-$sellerCouponAmount;
                $baseGrandTotal = round($baseGrandTotal, 4);

                $invoice = $this->invoiceService->prepareInvoice($order, $itemsarray['data']);
                $invoice->setTransactionId($transactionId);
                $invoice->setRequestedCaptureCase('online');

                $invoice->setShippingAmount($orderShippingAmount);
                $invoice->setBaseShippingAmount($shippingAmount);

                $invoice->setShippingInclTax($orderShippingAmount);
                $invoice->setBaseShippingInclTax($shippingAmount);

                $invoice->setSubtotal($subtotal);

                $invoice->setBaseSubtotal($baseSubtotal);

                if ($paymentCode == 'mpcashondelivery') {
                    $invoice->setMpcashondelivery($codCharges);
                }

                $invoice->setGrandTotal($grandTotal);
                $invoice->setBaseGrandTotal($baseGrandTotal);
                $newTax=0;

                $invoice->register();
                $invoice->save();
                $invoice->getOrder()->setIsInProcess(true);

                $transactionSave = $this->dbTransaction->addObject(
                    $invoice
                )->addObject(
                    $invoice->getOrder()
                );
                $transactionSave->save();

                $invoiceId = $invoice->getId();

                $this->invoiceSender->send($invoice);

                $order = $this->orderRepository->get($orderId);
                $order->addStatusHistoryComment(
                    __('Notified customer about invoice #%1.', $invoice->getId())
                );
                $order->setIsCustomerNotified(true);
                $order->setState('processing');
                $order->setStatus('processing');
                $order->save();

                /*--update mpcod table records--*/
                if ($paymentCode == 'mpcashondelivery') {
                    $saleslistColl = $this->salesListCollection->create()
                        ->addFieldToFilter(
                            'order_id',
                            ['eq' => $orderId]
                        )
                        ->addFieldToFilter(
                            'seller_id',
                            ['eq' => $sellerId]
                        );
                    foreach ($saleslistColl as $saleslist) {
                        $saleslist->setCollectCodStatus(1);
                        $saleslist->save();
                    }
                }
                $trackingcoll = $this->mpOrdersCollection->create()
                    ->addFieldToFilter('order_id', $orderId)
                    ->addFieldToFilter('seller_id', $sellerId);
                foreach ($trackingcoll as $row) {
                    $row->setInvoiceId($invoiceId);
                    $row->save();
                }
            }
        } catch (\Exception $e) {
            $this->logData("Helper_Payment createSellerOrderInvoice : ".$e->getMessage());
        }
    }

    /**
     * Saves payment transaction
     *
     * @param array $order order
     * @param array $additionalInfo transaction data
     * @param string $transId payment gateway transaction Id
     * @return int|void
     */
    public function saveTransaction($order, $additionalInfo, $transId)
    {
        try {
            $formatedPrice = $order->getBaseCurrency()->formatTxt(
                $order->getGrandTotal()
            );

            $message = __('The captured amount is %1.', $formatedPrice);

            $payment = $order->getPayment();

            $payment->setLastTransId($transId);
            $payment->setTransactionId($transId);
            $payment->setAdditionalInformation(
                [Transaction::RAW_DETAILS => $additionalInfo]
            );

            $transaction = $this->transactionBuilder->setPayment($payment)
                ->setOrder($order)
                ->setTransactionId($transId)
                ->setAdditionalInformation(
                    [Transaction::RAW_DETAILS => $additionalInfo]
                )
                ->setFailSafe(true)
                ->build(Transaction::TYPE_CAPTURE);

            $trId = $transaction->save()->getId();
            if ($trId) {
                $payment->addTransactionCommentsToOrder(
                    $transaction,
                    $message
                );
                $payment->setParentTransactionId(null);
                $payment->save();
                $order->save();
            }
            return $trId;
        } catch (\Exception $e) {
            $this->logData("Helper_Payment saveTransaction : ".$e->getMessage());
        }
    }

    /**
     * Prepare shipping split data
     *
     * @param  array $quote current quote
     * @return array
     */
    public function prepareFinalShipping($quote)
    {

        $finalShipping = [];

        try {
            $shipData = $this->getShippingData($quote);
            $newvar = $shipData['newvar'];
            $shipmeth = $shipData['shipping_method'];
            $shippingInfo = $shipData['shipinf'];

            if (!empty($shippingInfo)) {
                foreach ($shippingInfo as $info) {
                    $sellerId = $info['seller'];
                    $finalShipping[$sellerId]['amount'] = $info['amount'];
                    $finalShipping[$sellerId]['method'] = $info['method'];
                }
            }

            if ($newvar == "" && empty($finalShipping)) {
                $shipmethod = explode('_', $shipmeth, 2);
                $groups =  $quote->getShippingAddress()
                    ->getGroupedAllShippingRates();

                foreach ($groups as $rates) {
                    foreach ($rates as $rate) {
                        if ($rate->getCode() == $shipmeth &&
                          $quote->getShippingAddress()->getShippingMethod()==$shipmeth
                        ) {
                            $shipPrice = $quote->getShippingAddress()->getBaseShippingAmount();
                            $taxAmount = $this->calculateTaxByPercent($shipPrice, $quote);
                            $finalShipping[0]['amount'] = $shipPrice + $taxAmount;
                            $finalShipping[0]['method'] = $shipmethod[1];
                            break;
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            $this->logData("Helper_Payment prepareFinalShipping : ".$e->getMessage());
        }
        return $finalShipping;
    }

    /**
     * Prepare payment split data
     *
     * @param array $quote current quote
     * @return array
     */
    public function prepareSplitPaymentData($quote)
    {
        try {
            $cartdata = [];
            $commission = 0;
            $sellerTaxAmount = 0;
            $adminTaxAmount = 0;
            $commissionDetail =[];
            $onlyAdminTaxAmount = 0;

            $shippingData = $this->prepareFinalShipping($quote);

            foreach ($quote->getAllItems() as $item) {
                if ($item->getParentItem()) {
                    continue;
                }
                if ($item->getProductType() == Product::PRODUCT_TYPE_BUNDLE) {
                    $childDiscountAmount = $this->calculateBundleProductDiscount($item->getId(), $quote);
                }
                $product = $item->getProduct();
                $invoiceprice = $item->getBaseRowTotal();

                $commissionData = $this->getCommissionData($item);
                $commissionData = $this->updateCommissionData($commissionData);
                $tempcoms = $commissionData['tempcoms'];
                $commissionDetail = $commissionData['commissionDetail'];

                $commission += $tempcoms;
                $price = $invoiceprice - $tempcoms;

                if (!isset($commissionDetail['id'])) {
                    $commissionDetail['id'] = 0;
                }
                $sellerdetails['id'] = $commissionDetail['id'];
                $sellerdetails['comission'] = $commission;
                $productprice = floatval($price);

                if (!$this->helper->getConfigTaxManage()) {
                    $adminTaxAmount += $item->getBaseTaxAmount();
                } else {
                    $sellerTaxAmount = $item->getBaseTaxAmount();
                }

                $realSellerId = $this->getRealSellerId($item);
                $couponAmount = $this->getSellerCouponAmount($realSellerId);
                $creditPoints = $this->getCreditPoints($realSellerId);
                $totalDiscountAmount = $couponAmount + $creditPoints;

                $totalSellerAmount = $productprice + $sellerTaxAmount;
                $onlyAdminAmount = $productprice;
                $onlyAdminTaxAmount += $sellerTaxAmount;
                $itemDiscountAmount = $item->getBaseDiscountAmount();

                if ($itemDiscountAmount <= 0 && isset($childDiscountAmount) && $childDiscountAmount > 0) {
                    $itemDiscountAmount = $childDiscountAmount;
                }
                if ($itemDiscountAmount > 0) {
                    $itemDiscountAmount = -$itemDiscountAmount;
                    $totalDiscountAmount += $itemDiscountAmount;
                }

                if (empty($cartdata)) {
                    if ($sellerdetails['id'] == 0) {
                        if ($this->helper->getConfigTaxManage()) {
                            $adminTaxAmount += $sellerTaxAmount;
                        }
                        $cartdata[$sellerdetails['id']]['items'][$item->getId()]=[
                            'product_id'=>$product->getId(),
                            'qty'=>$item->getQty(),
                            'amount'=>$onlyAdminAmount,
                            'discount' => ($totalDiscountAmount < 0) ? $totalDiscountAmount : 0
                        ];
                        $cartdata[$sellerdetails['id']]['total'] = $onlyAdminAmount;
                    } else {
                        $cartdata[$sellerdetails['id']]['items'][$item->getId()]=[
                            'product_id'=>$product->getId(),
                            'qty'=>$item->getQty(),
                            'amount'=>$totalSellerAmount,
                            'discount' => ($totalDiscountAmount < 0) ? $totalDiscountAmount : 0
                        ];
                        $cartdata[$sellerdetails['id']]['total'] = $totalSellerAmount;
                    }
                    if (!empty($shippingData[$sellerdetails['id']])) {
                        $cartdata[$sellerdetails['id']]['shipping'] = $shippingData[$sellerdetails['id']];
                        $cartdata[$sellerdetails['id']]['total'] += $shippingData[$sellerdetails['id']]['amount'];
                    }
                } else {
                    $cartdata = $this->getCartData(
                        $cartdata,
                        $sellerdetails,
                        $sellerTaxAmount,
                        $adminTaxAmount,
                        $item,
                        $product,
                        $onlyAdminAmount,
                        $totalDiscountAmount,
                        $totalSellerAmount,
                        $shippingData
                    );
                }
            }
            if ($commission > 0) {
                $cartdata[0]['commission'] = $commission + $adminTaxAmount;
                if (!empty($cartdata[0]['total'])) {
                    $cartdata[0]['total'] += $commission + $adminTaxAmount;
                } else {
                    $cartdata[0]['total'] = $commission + $adminTaxAmount;
                }
            } elseif (count($cartdata)==1 && !empty($cartdata[0])) {
                if ($onlyAdminTaxAmount > 0) {
                    $cartdata[0]['tax'] = $onlyAdminTaxAmount;
                }
            }
            return $cartdata;
        } catch (\Exception $e) {
            $this->logData("Helper_Payment prepareSplitPaymentData : ".$e->getMessage());
            return [];
        }
    }

    /**
     * GetCartData
     *
     * @param array $cartdata
     * @param array $sellerdetails
     * @param float $sellerTaxAmount
     * @param float $adminTaxAmount
     * @param \Magento\Sales\Model\Order\Item $item
     * @param \Magento\Catalog\Model\Product $product
     * @param float $onlyAdminAmount
     * @param float $totalDiscountAmount
     * @param float $totalSellerAmount
     * @param array $shippingData
     * @return array
     */
    public function getCartData(
        $cartdata,
        $sellerdetails,
        $sellerTaxAmount,
        $adminTaxAmount,
        $item,
        $product,
        $onlyAdminAmount,
        $totalDiscountAmount,
        $totalSellerAmount,
        $shippingData
    ) {
        $flag=true;
        foreach ($cartdata as $key => $values) {
            if ($key==$sellerdetails['id']) {
                if ($key == 0) {
                    if ($this->helper->getConfigTaxManage()) {
                        $adminTaxAmount += $sellerTaxAmount;
                    }
                    $cartdata[$key]['items'][$item->getId()]=[
                        'product_id'=>$product->getId(),
                        'qty'=>$item->getQty(),
                        'amount'=>$onlyAdminAmount,
                        'discount' => ($totalDiscountAmount < 0) ? $totalDiscountAmount : 0
                    ];
                    $cartdata[$key]['total'] += $onlyAdminAmount;
                } else {
                    $cartdata[$key]['items'][$item->getId()]=[
                        'product_id'=>$product->getId(),
                        'qty'=>$item->getQty(),
                        'amount'=>$totalSellerAmount,
                        'discount' => ($totalDiscountAmount < 0) ? $totalDiscountAmount : 0
                    ];
                    $cartdata[$key]['total'] += $totalSellerAmount;
                }
                if (!empty($shippingData[$key])) {
                    $cartdata[$key]['shipping'] = $shippingData[$key];
                    $cartdata[$key]['total'] += $shippingData[$key]['amount'];
                }
                $flag=false;
            }
        }
        if ($flag) {
            if ($sellerdetails['id'] == 0) {
                if ($this->helper->getConfigTaxManage()) {
                    $adminTaxAmount += $sellerTaxAmount;
                }
                $cartdata[$sellerdetails['id']]['items'][$item->getId()]=[
                    'product_id'=>$product->getId(),
                    'qty'=>$item->getQty(),
                    'amount'=>$onlyAdminAmount,
                    'discount' => ($totalDiscountAmount < 0) ? $totalDiscountAmount : 0
                ];
                if (!empty($cartdata[$sellerdetails['id']]['total'])) {
                    $cartdata[$sellerdetails['id']]['total'] += $onlyAdminAmount;
                } else {
                    $cartdata[$sellerdetails['id']]['total'] = $onlyAdminAmount;
                }
            } else {
                $cartdata[$sellerdetails['id']]['items'][$item->getId()]=[
                    'product_id'=>$product->getId(),
                    'qty'=>$item->getQty(),
                    'amount'=>$totalSellerAmount,
                    'discount' => ($totalDiscountAmount < 0) ? $totalDiscountAmount : 0
                ];
                if (!empty($cartdata[$sellerdetails['id']]['total'])) {
                    $cartdata[$sellerdetails['id']]['total'] += $totalSellerAmount;
                } else {
                    $cartdata[$sellerdetails['id']]['total'] = $totalSellerAmount;
                }
            }
            if (!empty($shippingData[$sellerdetails['id']])) {
                $cartdata[$sellerdetails['id']]['shipping'] = $shippingData[$sellerdetails['id']];
                $cartdata[$sellerdetails['id']]['total'] += $shippingData[$sellerdetails['id']]['amount'];
            }
        }

        return $cartdata;
    }

    /**
     * Updates commission data
     *
     * @param array $commissionData
     * @return array
     */
    public function updateCommissionData($commissionData)
    {
        try {
            $tempcoms = $commissionData['tempcoms'];
            $commissionDetail = $commissionData['commissionDetail'];
            if (!$tempcoms) {
                $commissionDetail = $this->getSellerDetail($commissionData['product_id']);

                if ($commissionDetail['id'] !== 0
                    && $commissionDetail['commission'] !== 0
                ) {
                    $tempcoms = round(
                        ($commissionData['row_total'] * $commissionDetail['commission']) / 100,
                        4
                    );
                }
            }
            return [
                'tempcoms' => $tempcoms,
                'commissionDetail' => $commissionDetail
            ];
        } catch (\Exception $e) {
            $this->logData("Helper_Payment updateCommissionData : ".$e->getMessage());
            return $commissionData;
        }
    }

    /**
     * Getcurrent currency amount
     *
     * @param \Magento\Sales\Model\Order $order
     * @param float $price
     * @return void|float
     */
    public function getCurrentCurrencyAmountbyorder($order, $price)
    {
        /*
        * Get Current Store Currency Rate
        */
        $currentCurrencyCode = $order->getOrderCurrencyCode();
        $baseCurrencyCode = $order->getBaseCurrencyCode();
        $allowedCurrencies = $this->helper->getConfigAllowCurrencies();
        $rates = $this->helper->getCurrencyRates(
            $baseCurrencyCode,
            array_values($allowedCurrencies)
        );
        if (empty($rates[$currentCurrencyCode])) {
            $rates[$currentCurrencyCode] = 1;
        }
        return $price * $rates[$currentCurrencyCode];
    }

    /**
     * Log data
     *
     * @param array $data
     */
    public function logData($data)
    {
        $this->logger->info($data);
    }
}
