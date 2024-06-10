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
namespace Webkul\Marketplace\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Session\SessionManager;
use Magento\Quote\Model\QuoteRepository;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Webkul\Marketplace\Helper\Data as MarketplaceHelper;
use Webkul\Marketplace\Model\ResourceModel\Seller\CollectionFactory;
use Webkul\Marketplace\Model\OrdersFactory;
use Webkul\Marketplace\Model\SaleslistFactory;
use Magento\Sales\Model\Order\AddressFactory;
use Magento\Directory\Model\CountryFactory;
use Webkul\Marketplace\Helper\Email as MpEmailHelper;
use Webkul\Marketplace\Helper\Orders as OrdersHelper;
use Webkul\Marketplace\Model\ProductFactory;

/**
 * Webkul Marketplace SalesOrderInvoiceSaveAfterObserver Observer Model.
 */
class SalesOrderInvoiceSaveAfterObserver implements ObserverInterface
{
    /**
     * @var eventManager
     */
    protected $_eventManager;

    /**
     * @var Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @var Magento\Checkout\Model\Session
     */
    protected $_checkoutSession;

    /**
     * @var SessionManager
     */
    protected $_coreSession;

    /**
     * @var QuoteRepository
     */
    protected $_quoteRepository;

    /**
     * @var OrderRepositoryInterface
     */
    protected $_orderRepository;

    /**
     * @var CustomerRepositoryInterface
     */
    protected $_customerRepository;

    /**
     * @var ProductRepositoryInterface
     */
    protected $_productRepository;

    /**
     * @var MarketplaceHelper
     */
    protected $_marketplaceHelper;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $_date;

    /**
     * @var CollectionFactory
     */
    protected $sellerCollection;

    /**
     * @var OrdersFactory
     */
    protected $ordersFactory;

    /**
     * @var SaleslistFactory
     */
    protected $saleslistFactory;

    /**
     * @var AddressFactory
     */
    protected $orderAddressFactory;

    /**
     * @var CountryFactory
     */
    protected $countryModel;

    /**
     * @var MpEmailHelper
     */
    protected $mpEmailHelper;

    /**
     * @var OrdersHelper
     */
    protected $ordersHelper;

    /**
     * @var ProductFactory
     */
    protected $productFactory;

    /**
     * @param \Magento\Framework\Event\Manager            $eventManager
     * @param \Magento\Customer\Model\Session             $customerSession
     * @param \Magento\Checkout\Model\Session             $checkoutSession
     * @param SessionManager                              $coreSession
     * @param QuoteRepository                             $quoteRepository
     * @param OrderRepositoryInterface                    $orderRepository
     * @param CustomerRepositoryInterface                 $customerRepository
     * @param ProductRepositoryInterface                  $productRepository
     * @param MarketplaceHelper                           $marketplaceHelper
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $date
     * @param CollectionFactory                           $sellerCollection
     * @param OrdersFactory                               $ordersFactory
     * @param SaleslistFactory                            $saleslistFactory
     * @param AddressFactory                              $orderAddressFactory
     * @param CountryFactory                              $countryModel
     * @param MpEmailHelper                               $mpEmailHelper
     * @param OrdersHelper                                $ordersHelper
     * @param ProductFactory                              $productFactory
     */
    public function __construct(
        \Magento\Framework\Event\Manager $eventManager,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Checkout\Model\Session $checkoutSession,
        SessionManager $coreSession,
        QuoteRepository $quoteRepository,
        OrderRepositoryInterface $orderRepository,
        CustomerRepositoryInterface $customerRepository,
        ProductRepositoryInterface $productRepository,
        MarketplaceHelper $marketplaceHelper,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        CollectionFactory $sellerCollection,
        OrdersFactory $ordersFactory,
        SaleslistFactory $saleslistFactory,
        AddressFactory $orderAddressFactory,
        CountryFactory $countryModel,
        MpEmailHelper $mpEmailHelper,
        OrdersHelper $ordersHelper,
        ProductFactory $productFactory
    ) {
        $this->_eventManager = $eventManager;
        $this->_customerSession = $customerSession;
        $this->_checkoutSession = $checkoutSession;
        $this->_coreSession = $coreSession;
        $this->_quoteRepository = $quoteRepository;
        $this->_orderRepository = $orderRepository;
        $this->_customerRepository = $customerRepository;
        $this->_productRepository = $productRepository;
        $this->_marketplaceHelper = $marketplaceHelper;
        $this->_date = $date;
        $this->sellerCollection = $sellerCollection;
        $this->ordersFactory = $ordersFactory;
        $this->saleslistFactory = $saleslistFactory;
        $this->orderAddressFactory = $orderAddressFactory;
        $this->countryModel = $countryModel;
        $this->mpEmailHelper = $mpEmailHelper;
        $this->ordersHelper = $ordersHelper;
        $this->productFactory = $productFactory;
    }

    /**
     * Sales Order Invoice Save After event handler.
     *
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $event = $observer->getInvoice();
        $invoiceId = $observer->getInvoice()->getId();
        $order = $observer->getInvoice()->getOrder();
        $lastOrderId = $order->getId();

        $resultArr = $this->getSellerArray($event->getAllItems());

        $sellerItemsArray = $resultArr['seller_items'];
        $sellerInvoicedItems = $resultArr['seller_invoice_items'];
        $sellerInvoicedItemsQtyArr = $resultArr['seller_invoice_items_qty'];
        $invoiceSellerIds = $resultArr['invoice_seller_ids'];

        /*send placed order mail notification to seller*/
        $paymentCode = '';
        if ($order->getPayment()) {
            $paymentCode = $order->getPayment()->getMethod();
        }
        if ($paymentCode == 'mpcashondelivery') {
            $saleslistColl = $this->saleslistFactory->create()
                              ->getCollection()
                              ->addFieldToFilter(
                                  'seller_id',
                                  ['in' => $invoiceSellerIds]
                              )
                              ->addFieldToFilter(
                                  'order_id',
                                  $lastOrderId
                              )
                              ->addFieldToFilter(
                                  'order_item_id',
                                  ['in' => $sellerInvoicedItems]
                              );
            foreach ($saleslistColl as $saleslist) {
                $saleslist->setCollectCodStatus(1);
                $saleslist->save();
            }
        }
        $shippingInfo = '';
        $shippingDes = '';

        $billingId = $order->getBillingAddress()->getId();

        $billaddress = $this->orderAddressFactory->create()->load($billingId);
        $billinginfo = $billaddress['firstname'].'<br/>'.
        $billaddress['street'].'<br/>'.
        $billaddress['city'].' '.
        $billaddress['region'].' '.
        $billaddress['postcode'].'<br/>'.
        $this->countryModel->create()->load($billaddress['country_id'])->getName().'<br/>T:'.
        $billaddress['telephone'];

        $payment = $order->getPayment()->getMethodInstance()->getTitle();

        if ($order->getShippingAddress()) {
            $shippingId = $order->getShippingAddress()->getId();
            $address = $this->orderAddressFactory->create()->load($shippingId);
            $shippingInfo = $address['firstname'].'<br/>'.
            $address['street'].'<br/>'.
            $address['city'].' '.
            $address['region'].' '.
            $address['postcode'].'<br/>'.
            $this->countryModel->create()->load($address['country_id'])->getName().'<br/>T:'.
            $address['telephone'];
            $shippingDes = $order->getShippingDescription();
        }

        $adminStoreEmail = $this->_marketplaceHelper->getAdminEmailId();
        $defaultTransEmailId = $this->_marketplaceHelper->getDefaultTransEmailId();
        $adminEmail = $adminStoreEmail ? $adminStoreEmail : $defaultTransEmailId;
        $adminUsername = $this->_marketplaceHelper->getAdminName();

        $sellerOrder = $this->ordersFactory->create()
                        ->getCollection()
                        ->addFieldToFilter('seller_id', ['in' => $invoiceSellerIds])
                        ->addFieldToFilter('order_id', $lastOrderId)
                        ->addFieldToFilter('seller_id', ['neq' => 0]);
        $saveInvoiceIds = [];
        foreach ($sellerOrder as $info) {
            if ($info->getInvoiceId()) {
                $saveInvoiceIds = explode(',', $info->getInvoiceId());
            }
            if (!in_array($invoiceId, $saveInvoiceIds)) {
                array_push($saveInvoiceIds, $invoiceId);
                $saveInvoiceIds = array_unique($saveInvoiceIds);
                $allInvoiceIds = implode(',', $saveInvoiceIds);
                $info->setInvoiceId($allInvoiceIds);
                $info->setOrderStatus('processing')->save();
            }
            $userdata = $this->_customerRepository->getById($info['seller_id']);
            $username = $userdata->getFirstname();
            $useremail = $userdata->getEmail();

            $senderInfo = [];
            $receiverInfo = [];

            $receiverInfo = [
                'name' => $username,
                'email' => $useremail,
            ];
            $senderInfo = [
                'name' => $adminUsername,
                'email' => $adminEmail,
            ];
            $totalprice = 0;
            $totalTaxAmount = 0;
            $couponAmount = 0;
            $codCharges = 0;
            $shippingCharges = 0;
            $mailData = [];
            $currencyRate = 0;
            $saleslistIds = [];
            $codChargeAmount = 0;
            $collectionSales = $this->saleslistFactory->create()
            ->getCollection()
            ->addFieldToFilter('order_id', $lastOrderId)
            ->addFieldToFilter('seller_id', $info['seller_id'])
            ->addFieldToFilter('parent_item_id', ['null' => 'true'])
            ->addFieldToFilter(
                'order_item_id',
                ['in' => $sellerInvoicedItems]
            )
            ->addFieldToFilter('magerealorder_id', ['neq' => 0])
            ->addFieldToSelect('entity_id');

            $saleslistIds = $collectionSales->getData();

            $fetchsale = $this->saleslistFactory->create()
            ->getCollection()
            ->addFieldToFilter(
                'entity_id',
                ['in' => $saleslistIds]
            );
            $fetchsale->getSellerOrderCollection();
            foreach ($fetchsale as $res) {
                $currencyRate = $res->getCurrencyRate();
                $product = $this->_productRepository->getById($res['mageproduct_id']);

                $orderedQty = $res['magequantity'];

                if (isset($sellerInvoicedItemsQtyArr[$res['order_item_id']])) {
                    $invoiceQty = $sellerInvoicedItemsQtyArr[$res['order_item_id']];
                } else {
                    $invoiceQty = $res['magequantity'];
                }

                /* product name */
                $productName = $res->getMageproName();
                $result = [];
                $result = $this->getProductOptionData($res, $result);
                $productName = $this->getProductNameHtml($result, $productName);
                /* end */

                $sku = $product->getSku();
                $mailData["items"][] = [
                    "productName" => $productName,
                    "sku" => $sku,
                    "qty" => ($invoiceQty * 1),
                    "price" => strip_tags($order->formatPrice($this->_marketplaceHelper
                    ->getCurrentCurrencyPrice(
                        $currencyRate,
                        $res['magepro_price'] * $invoiceQty
                    )))
                ];
                $appliedTax = $res['total_tax'] / $orderedQty;
                $totalTaxAmount = $totalTaxAmount + ($appliedTax * $invoiceQty);
                $totalprice = $totalprice + ($res['magepro_price'] * $invoiceQty);
                if ($res['is_coupon']) {
                    $appliedAmount = $res['applied_coupon_amount'] / $orderedQty;
                    $couponAmount = $couponAmount + ($appliedAmount * $invoiceQty);
                }
                if ($paymentCode == 'mpcashondelivery') {
                    $appliedCodCharges = $res->getCodCharges() / $orderedQty;
                    $codCharges = $codCharges + ($appliedCodCharges * $invoiceQty);
                }
            }
            $shippingCharges = $info->getShippingCharges();
            if ($paymentCode == 'mpcashondelivery') {
                $codChargeAmount = strip_tags($order->formatPrice($this->_marketplaceHelper
                ->getCurrentCurrencyPrice($currencyRate, $codCharges)));
            }
            $mailData["shipping"] = strip_tags($order->formatPrice($this->_marketplaceHelper
            ->getCurrentCurrencyPrice($currencyRate, $shippingCharges)));
            $mailData["discount"] = strip_tags($order->formatPrice($this->_marketplaceHelper
            ->getCurrentCurrencyPrice($currencyRate, $couponAmount)));
            $mailData["taxAmount"] = strip_tags($order->formatPrice($this->_marketplaceHelper
            ->getCurrentCurrencyPrice($currencyRate, $totalTaxAmount)));
            $mailData["codChargeAmount"] = $codChargeAmount;
            $mailData["taxAmount"] = strip_tags($order->formatPrice($this->_marketplaceHelper
            ->getCurrentCurrencyPrice($currencyRate, $totalTaxAmount)));
            $mailData["grandTotal"] = strip_tags($order->formatPrice($this->_marketplaceHelper
            ->getCurrentCurrencyPrice(
                $currencyRate,
                $totalprice +
                $totalTaxAmount +
                $shippingCharges +
                $codCharges -
                $couponAmount
            )));

            if ($shippingInfo != '') {
                $isNotVirtual = 1;
            } else {
                $isNotVirtual = 0;
            }
            $emailTempVariables['realOrderId'] = $order->getRealOrderId();
            $emailTempVariables['createdAt'] = $order['created_at'];
            $emailTempVariables['username'] = $username;
            $emailTempVariables['billingInfo'] = $billinginfo;
            $emailTempVariables['payment'] = $payment;
            $emailTempVariables['shippingInfo'] = $shippingInfo;
            $emailTempVariables['isNotVirtual'] = $isNotVirtual;
            $emailTempVariables['mailData'] = $mailData;
            $emailTempVariables['shippingDesc'] = $shippingDes;
            $this->mpEmailHelper->sendInvoicedOrderEmail(
                $emailTempVariables,
                $senderInfo,
                $receiverInfo
            );
        }
        /*
        * Marketplace Order product sold Observer
        */
        $this->_eventManager->dispatch(
            'mp_product_sold',
            ['itemwithseller' => $sellerItemsArray]
        );
    }

    /**
     * Get seller details
     *
     * @param array $items
     * @return array
     */
    public function getSellerArray($items)
    {
        $sellerItemsArray = [];
        $sellerInvoicedItemsArray = [];
        $sellerInvoicedItemsQtyArray = [];
        $invoiceSellerIds = [];
        $resultArr = [];

        foreach ($items as $value) {
            $invoiceproduct = $value->getData();
            $proSellerId = 0;
            $productSeller = $this->productFactory->create()
            ->getCollection()
            ->addFieldToFilter(
                'mageproduct_id',
                $invoiceproduct['product_id']
            );
            foreach ($productSeller as $sellervalue) {
                if ($sellervalue->getSellerId()) {
                    $invoiceSellerIds[$sellervalue->getSellerId()] = $sellervalue->getSellerId();
                    $proSellerId = $sellervalue->getSellerId();
                }
            }
            if ($proSellerId) {
                $sellerItemsArray[$proSellerId][] = $invoiceproduct;
                array_push($sellerInvoicedItemsArray, $value->getOrderItemId());
                $sellerInvoicedItemsQtyArray[$value->getOrderItemId()] = $value->getQty();
            }
        }
        $resultArr['seller_items'] = $sellerItemsArray;
        $resultArr['seller_invoice_items'] = $sellerInvoicedItemsArray;
        $resultArr['seller_invoice_items_qty'] = $sellerInvoicedItemsQtyArray;
        $resultArr['invoice_seller_ids'] = $invoiceSellerIds;
        return $resultArr;
    }

    /**
     * Get Order Product Option Data Method.
     *
     * @param \Magento\Sales\Model\Order\Item $item
     * @param array                           $result
     *
     * @return array
     */
    public function getProductOptionData($item, $result = [])
    {
        $productOptionsData = $this->ordersHelper->getProductOptions(
            $item->getProductOptions()
        );
        if ($options = $productOptionsData) {
            if (isset($options['options'])) {
                $result = array_merge($result, $options['options']);
            }
            if (isset($options['additional_options'])) {
                $result = array_merge($result, $options['additional_options']);
            }
            if (isset($options['attributes_info'])) {
                $result = array_merge($result, $options['attributes_info']);
            }
        }

        return $result;
    }

    /**
     * Get Order Product Name Html Data Method.
     *
     * @param array  $result
     * @param string $productName
     *
     * @return string
     */
    public function getProductNameHtml($result, $productName)
    {
        if ($_options = $result) {
            $proOptionData = '<dl class="item-options">';
            foreach ($_options as $_option) {
                $proOptionData .= '<dt>'.$_option['label'].'</dt>';

                $proOptionData .= '<dd>'.$_option['value'];
                $proOptionData .= '</dd>';
            }
            $proOptionData .= '</dl>';
            $productName = $productName.'<br/>'.$proOptionData;
        } else {
            $productName = $productName.'<br/>';
        }

        return $productName;
    }
}
