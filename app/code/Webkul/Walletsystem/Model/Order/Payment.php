<?php

namespace Webkul\Walletsystem\Model\Order;

use Magento\Framework\App\ObjectManager;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Payment\Model\SaleOperationInterface;
use Magento\Sales\Api\Data\OrderPaymentInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Payment\Info;
use Magento\Sales\Model\Order\Payment\Operations\SaleOperation;
use Magento\Sales\Model\Order\Payment\Transaction;
use Magento\Sales\Model\Order\Payment\Transaction\ManagerInterface;
use Magento\Sales\Api\CreditmemoManagementInterface as CreditmemoManager;
use Magento\Sales\Model\Order\Payment as PaymentModel;
use Magento\Sales\Model\Order\OrderStateResolverInterface;
use Magento\Sales\Model\Order\Creditmemo;

class Payment extends PaymentModel
{
    /**
     * @var OrderStateResolverInterface
     */
    private $orderStateResolver;

    /**
     * @var CreditmemoManager
     */
    private $creditmemoManager = null;

    /**
     * @var SaleOperation
     */
    private $saleOperation;

    /**
     * Updates payment totals, updates order status and adds proper comments
     *
     * @param \Magento\Sales\Model\Order\Creditmemo $creditmemo
     * @return $this
     */
    public function refund($creditmemo)
    {
        $baseAmountToRefund = $this->formatAmount($creditmemo->getBaseGrandTotal());
        $this->setTransactionId(
            $this->transactionManager->generateTransactionId($this, Transaction::TYPE_REFUND)
        );

        $isOnline = false;
        $gateway = $this->getMethodInstance();
        $invoice = null;
        if ($gateway->canRefund()) {
            $this->setCreditmemo($creditmemo);
            if ($creditmemo->getDoTransaction()) {
                $invoice = $creditmemo->getInvoice();
                if ($invoice) {
                    $isOnline = true;
                    $captureTxn = $this->transactionRepository->getByTransactionId(
                        $invoice->getTransactionId(),
                        $this->getId(),
                        $this->getOrder()->getId()
                    );
                    if ($captureTxn) {
                        $this->setTransactionIdsForRefund($captureTxn);
                    }
                    $this->setShouldCloseParentTransaction(true);
                    try {
                        $gateway->setStore(
                            $this->getOrder()->getStoreId()
                        );
                        $this->setRefundTransactionId($invoice->getTransactionId());
                        $gateway->refund($this, $baseAmountToRefund);

                        $creditmemo->setTransactionId($this->getLastTransId());
                    } catch (\Magento\Framework\Exception\LocalizedException $e) {
                        if (!$captureTxn) {
                            throw new \Magento\Framework\Exception\LocalizedException(
                                __('If the invoice was created offline, try creating an offline credit memo.'),
                                $e
                            );
                        }
                        throw $e;
                    }
                }
            } elseif ($gateway->isOffline()) {
                $gateway->setStore(
                    $this->getOrder()->getStoreId()
                );
                $gateway->refund($this, $baseAmountToRefund);
            }
        }

        $this->_updateTotals(
            [
                'amount_refunded' => $creditmemo->getGrandTotal(),
                'base_amount_refunded' => $baseAmountToRefund,
                'base_amount_refunded_online' => $isOnline ? $baseAmountToRefund : null,
                'shipping_refunded' => $creditmemo->getShippingAmount(),
                'base_shipping_refunded' => $creditmemo->getBaseShippingAmount(),
            ]
        );

        $transaction = $this->addTransaction(
            Transaction::TYPE_REFUND,
            $creditmemo,
            $isOnline
        );

        if ($invoice) {
            $message = __('We refunded %1 online.', $this->formatPrice($baseAmountToRefund));
        } else {
            $message = $this->hasMessage() ? $this->getMessage() : __(
                'We refunded %1 offline.',
                $this->formatPrice($baseAmountToRefund)
            );
        }

        if (!$isOnline && !$invoice) {
            $message = $this->hasMessage() ? $this->getMessage() : __(
                'We refunded %1 in Webkul Wallet.',
                $this->formatPrice($baseAmountToRefund)
            );

        }

        $message = $this->prependMessage($message);
        $message = $this->_appendTransactionToMessage($transaction, $message);
        $orderState = $this->getOrderStateResolver()->getStateForOrder($this->getOrder());
        $statuses = $this->getOrder()->getConfig()->getStateStatuses($orderState, false);
        $status = in_array($this->getOrder()->getStatus(), $statuses, true)
            ? $this->getOrder()->getStatus()
            : $this->getOrder()->getConfig()->getStateDefaultStatus($orderState);

        $this->getOrder()->addStatusHistoryComment(
            $message,
            $status
        )->setIsCustomerNotified($creditmemo->getOrder()->getCustomerNoteNotify());
        $this->_eventManager->dispatch(
            'sales_order_payment_refund',
            ['payment' => $this, 'creditmemo' => $creditmemo]
        );

        return $this;
    }

    /**
     * Get order state resolver instance.
     *
     * @return Magento\Sales\Model\Order\OrderStateResolverInterface
     */
    private function getOrderStateResolver()
    {
        if ($this->orderStateResolver === null) {
            $this->orderStateResolver = ObjectManager::getInstance()->get(OrderStateResolverInterface::class);
        }

        return $this->orderStateResolver;
    }

    /**
     * Set payment parent transaction id and current transaction id if it not set
     *
     * @param Transaction $transaction
     */
    private function setTransactionIdsForRefund(Transaction $transaction)
    {
        if (!$this->getTransactionId()) {
            $this->setTransactionId(
                $this->transactionManager->generateTransactionId(
                    $this,
                    Transaction::TYPE_REFUND,
                    $transaction
                )
            );
        }
        $this->setParentTransactionId($transaction->getTxnId());
    }

    /**
     * Collects order invoices totals by provided keys.
     *
     * @param Order $order
     * @param array $keys
     */
    private function collectTotalAmounts(Order $order, array $keys)
    {
        $result = array_fill_keys($keys, 0.00);
        $invoiceCollection = $order->getInvoiceCollection();
        /** @var Invoice $invoice */
        foreach ($invoiceCollection as $invoice) {
            foreach ($keys as $key) {
                $result[$key] += $invoice->getData($key);
            }
        }

        return $result;
    }

    /**
     * Check sale operation availability for payment method.
     *
     * @return bool
     */
    private function canSale(): bool
    {
        $method = $this->getMethodInstance();

        return $method instanceof SaleOperationInterface && $method->canSale();
    }
}
