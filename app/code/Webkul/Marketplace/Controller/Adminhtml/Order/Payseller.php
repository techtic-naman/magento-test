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

namespace Webkul\Marketplace\Controller\Adminhtml\Order;

use Magento\Framework\Controller\ResultFactory;
use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use Webkul\Marketplace\Model\ResourceModel\Saleslist\CollectionFactory;
use Webkul\Marketplace\Helper\Data as MpHelper;
use Webkul\Marketplace\Helper\Email as MpEmailHelper;
use Webkul\Marketplace\Model\SellertransactionFactory;
use Webkul\Marketplace\Model\SaleperpartnerFactory;
use Webkul\Marketplace\Model\OrdersFactory;
use Webkul\Marketplace\Helper\Notification as NotificationHelper;

/**
 * Class Payseller used to Payseller.
 */
class Payseller extends \Magento\Backend\App\Action
{
    /**
     * @var Filter
     */
    public $filter;

    /**
     * @var CollectionFactory
     */
    public $collectionFactory;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    public $date;

    /**
     * @var \Magento\Framework\Stdlib\DateTime
     */
    public $dateTime;

    /** @var \Magento\Sales\Model\OrderRepository */
    public $orderRepository;

    /**
     * @var MpHelper
     */
    protected $mpHelper;

    /**
     * @var MpEmailHelper
     */
    protected $mpEmailHelper;

    /**
     * @var SellertransactionFactory
     */
    protected $sellertransaction;

    /**
     * @var SaleperpartnerFactory
     */
    protected $saleperpartner;

    /**
     * @var OrdersFactory
     */
    protected $ordersModel;

    /**
     * @var NotificationHelper
     */
    protected $notificationHelper;

    /**
     * @var \Magento\Customer\Model\CustomerFactory
     */
    protected $customerModel;

    /**
     * @var \Webkul\Marketplace\Model\SaleslistFactory
     */
    protected $salesListModel;

    /**
     * @param Context                                     $context
     * @param Filter                                      $filter
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $date
     * @param \Magento\Framework\Stdlib\DateTime          $dateTime
     * @param \Magento\Sales\Model\OrderRepository        $orderRepository
     * @param CollectionFactory                           $collectionFactory
     * @param MpHelper                                    $mpHelper
     * @param MpEmailHelper                               $mpEmailHelper
     * @param SellertransactionFactory                    $sellertransaction
     * @param SaleperpartnerFactory                       $saleperpartner
     * @param OrdersFactory                               $ordersModel
     * @param NotificationHelper                          $notificationHelper
     * @param \Magento\Customer\Model\CustomerFactory     $customerModel
     * @param \Webkul\Marketplace\Model\SaleslistFactory  $salesListModel
     */
    public function __construct(
        Context $context,
        Filter $filter,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Magento\Framework\Stdlib\DateTime $dateTime,
        \Magento\Sales\Model\OrderRepository $orderRepository,
        CollectionFactory $collectionFactory,
        MpHelper $mpHelper,
        MpEmailHelper $mpEmailHelper,
        SellertransactionFactory $sellertransaction,
        SaleperpartnerFactory $saleperpartner,
        OrdersFactory $ordersModel,
        NotificationHelper $notificationHelper,
        \Magento\Customer\Model\CustomerFactory $customerModel,
        \Webkul\Marketplace\Model\SaleslistFactory $salesListModel
    ) {
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        $this->date = $date;
        $this->dateTime = $dateTime;
        $this->orderRepository = $orderRepository;
        $this->mpHelper = $mpHelper;
        $this->mpEmailHelper = $mpEmailHelper;
        $this->sellertransaction = $sellertransaction;
        $this->saleperpartner = $saleperpartner;
        $this->ordersModel = $ordersModel;
        $this->notificationHelper = $notificationHelper;
        $this->customerModel = $customerModel;
        $this->salesListModel = $salesListModel;
        parent::__construct($context);
    }

    /**
     * Execute action.
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     *
     * @throws \Magento\Framework\Exception\LocalizedException|\Exception
     */
    public function execute()
    {
        try {
            $wholedata = $this->getRequest()->getParams();
            $actparterprocost = 0;
            $totalamount = 0;
            $sellerId = $wholedata['seller_id'];
            $helper = $this->mpHelper;
            $taxToSeller = $helper->getConfigTaxManage();
            $collection = $this->collectionFactory->create()
            ->addFieldToFilter('entity_id', $wholedata['autoorderid'])
            ->addFieldToFilter('order_id', ['neq' => 0])
            ->addFieldToFilter('paid_status', 0)
            ->addFieldToFilter('cpprostatus', ['neq' => 0]);
            $mailItems = [];
            foreach ($collection as $row) {
                $sellerId = $row->getSellerId();
                $order = $this->orderRepository->get($row['order_id']);
                $taxAmount = $row['total_tax'];
                $marketplaceOrders = $this->ordersModel->create()
                ->getCollection()
                ->addFieldToFilter('order_id', $row['order_id'])
                ->addFieldToFilter('seller_id', $sellerId);
                foreach ($marketplaceOrders as $tracking) {
                    $taxToSeller = $tracking['tax_to_seller'];
                }
                $vendorTaxAmount = 0;
                if ($taxToSeller) {
                    $vendorTaxAmount = $taxAmount;
                }
                $codCharges = 0;
                $shippingCharges = 0;
                if (!empty($row['cod_charges'])) {
                    $codCharges = $row->getCodCharges();
                }
                if ($row->getIsShipping() == 1) {
                    foreach ($marketplaceOrders as $tracking) {
                        $shippingamount = $tracking->getShippingCharges();
                        $refundedShippingAmount = $tracking->getRefundedShippingCharges();
                        $shippingCharges = $shippingamount - $refundedShippingAmount;
                    }
                }
                $actparterprocost = $actparterprocost +
                    $row->getActualSellerAmount() +
                    $vendorTaxAmount +
                    $codCharges +
                    $shippingCharges;
                $totalamount = $totalamount +
                    $row->getTotalAmount() +
                    $taxAmount +
                    $codCharges +
                    $shippingCharges -
                    $row->getAppliedCouponAmount();
                    $mailItems[] = [
                        "orderId" => $row['magerealorder_id'],
                        "productName" => $row['magepro_name'],
                        "qty" => $row['magequantity'],
                        "price" => strip_tags($order->formatBasePrice($row['magepro_price'])),
                        "commission" => strip_tags($order->formatBasePrice($totalamount - $actparterprocost)),
                        "totalPayout" => strip_tags($order->formatBasePrice($actparterprocost))
                    ];
            }
            if ($actparterprocost) {
                $collectionverifyread = $this->saleperpartner->create()
                ->getCollection()
                ->addFieldToFilter('seller_id', $sellerId);
                if (count($collectionverifyread) >= 1) {
                    $id = 0;
                    $totalremain = 0;
                    $amountpaid = 0;
                    foreach ($collectionverifyread as $verifyrow) {
                        $id = $verifyrow->getId();
                        if ($verifyrow->getAmountRemain() >= $actparterprocost) {
                            $totalremain = $verifyrow->getAmountRemain() - $actparterprocost;
                        }
                        $amountpaid = $verifyrow->getAmountReceived();
                    }
                    $verifyrow = $this->saleperpartner->create()->load($id);
                    $totalrecived = $actparterprocost + $amountpaid;
                    $verifyrow->setLastAmountPaid($actparterprocost);
                    $verifyrow->setAmountReceived($totalrecived);
                    $verifyrow->setAmountRemain($totalremain);
                    $verifyrow->save();
                } else {
                    $percent = $helper->getConfigCommissionRate();
                    $collectionf = $this->saleperpartner->create();
                    $collectionf->setSellerId($sellerId);
                    $collectionf->setTotalSale($totalamount);
                    $collectionf->setLastAmountPaid($actparterprocost);
                    $collectionf->setAmountReceived($actparterprocost);
                    $collectionf->setAmountRemain(0);
                    $collectionf->setCommissionRate($percent);
                    $collectionf->setTotalCommission($totalamount - $actparterprocost);
                    $collectionf->setCreatedAt($this->date->gmtDate());
                    $collectionf->save();
                }

                $uniqueId = $this->checktransid();
                $transid = '';
                $transactionNumber = '';
                if ($uniqueId != '') {
                    $sellerTrans = $this->sellertransaction->create()
                    ->getCollection()
                    ->addFieldToFilter('transaction_id', $uniqueId);
                    if (count($sellerTrans)) {
                        $id = 0;
                        foreach ($sellerTrans as $value) {
                            $id = $value->getId();
                        }
                        if ($id) {
                            $this->sellertransaction->create()->load($id)->delete();
                        }
                    }
                    $sellerTrans = $this->sellertransaction->create();
                    $sellerTrans->setTransactionId($uniqueId);
                    $sellerTrans->setTransactionAmount($actparterprocost);
                    $sellerTrans->setType('Manual');
                    $sellerTrans->setMethod('Manual');
                    $sellerTrans->setSellerId($sellerId);
                    $sellerTrans->setCustomNote($wholedata['seller_pay_reason']);
                    $sellerTrans->setCreatedAt($this->date->gmtDate());
                    $sellerTrans->setSellerPendingNotification(1);
                    $sellerTrans = $sellerTrans->save();
                    $transid = $sellerTrans->getId();
                    $transactionNumber = $sellerTrans->getTransactionId();
                    $this->notificationHelper->saveNotification(
                        \Webkul\Marketplace\Model\Notification::TYPE_TRANSACTION,
                        $transid,
                        $transid
                    );
                }

                $collection = $this->salesListModel->create()->load($wholedata['autoorderid']);

                $cpprostatus = $collection->getCpprostatus();
                $paidStatus = $collection->getPaidStatus();
                $orderId = $collection->getOrderId();

                if ($cpprostatus == 1 && $paidStatus == 0 && $orderId != 0) {
                    $collection->setPaidStatus(1);
                    $collection->setTransId($transid)->save();
                    $data['trans_id'] = $transactionNumber;
                    $data['mp_trans_row_id'] = $transid;
                    $data['mp_saleslist_row_id'] = $collection->getId();
                    $data['id'] = $collection->getOrderId();
                    $data['seller_id'] = $collection->getSellerId();
                    $this->_eventManager->dispatch(
                        'mp_pay_seller',
                        [$data]
                    );
                }

                $seller = $this->customerModel->create()->load($sellerId);

                $emailTempVariables = [];

                $adminStoreEmail = $helper->getAdminEmailId();
                $adminEmail = $adminStoreEmail ? $adminStoreEmail : $helper->getDefaultTransEmailId();
                $adminUsername = $helper->getAdminName();

                $senderInfo = [];
                $receiverInfo = [];

                $receiverInfo = [
                    'name' => $seller->getName(),
                    'email' => $seller->getEmail(),
                ];
                $senderInfo = [
                    'name' => $adminUsername,
                    'email' => $adminEmail,
                ];

                $emailTempVariables['sellerName'] = $seller->getName();
                $emailTempVariables['transactionNumber'] = $transactionNumber;
                $emailTempVariables['createdAt'] = $this->date->gmtDate();
                $emailTempVariables['transactionAmount'] = $order->formatBasePrice($actparterprocost);
                $emailTempVariables['mailItems'] = $mailItems;
                $emailTempVariables['reason'] = $wholedata['seller_pay_reason'];
                $this->mpEmailHelper->sendSellerPaymentEmail(
                    $emailTempVariables,
                    $senderInfo,
                    $receiverInfo
                );

                $this->messageManager->addSuccess(__('Payment has been successfully done for this seller'));
            }
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->messageManager->addError($e->getMessage());
        } catch (\Exception $e) {
            $this->mpHelper->logDataInLogger(
                "controller_AdminHtml_Order_Payseller execute : ".$e->getMessage()
            );
            $this->messageManager->addError(__('We can\'t pay the seller right now. %1'));
        }
        
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

        return $resultRedirect->setPath('marketplace/order/index', ['seller_id' => $sellerId]);
    }

    /**
     * Get random string
     *
     * @param string $length
     * @param string $charset
     * @return string
     */
    public function randString(
        $length,
        $charset = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789'
    ) {
        $str = 'tr-';
        $count = strlen($charset);
        while ($length--) {
            $str .= $charset[random_int(0, $count - 1)];
        }

        return $str;
    }

    /**
     * Check transaction id
     *
     * @return string
     */
    public function checktransid()
    {
        $uniqueId = $this->randString(11);
        $collection = $this->sellertransaction->create()
        ->getCollection()
        ->addFieldToFilter('transaction_id', $uniqueId);
        $index = 0;
        foreach ($collection as $value) {
            ++$index;
        }
        if ($index != 0) {
            $this->checktransid();
        } else {
            return $uniqueId;
        }
        return $uniqueId;
    }

    /**
     * Check for is allowed.
     *
     * @return bool
     */
    public function _isAllowed()
    {
        return $this->_authorization->isAllowed('Webkul_Marketplace::seller');
    }
}
