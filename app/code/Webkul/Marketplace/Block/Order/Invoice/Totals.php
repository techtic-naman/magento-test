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
namespace Webkul\Marketplace\Block\Order\Invoice;

class Totals extends \Webkul\Marketplace\Block\Order\Totals
{
    /**
     * Get totals source object
     *
     * @return Order
     */
    public function getSource()
    {
        $collection = $this->orderCollection
        ->addFieldToFilter(
            'main_table.order_id',
            $this->getOrder()->getId()
        )->addFieldToFilter(
            'main_table.seller_id',
            $this->helper->getCustomerId()
        )->getSellerInvoiceTotals($this->getInvoice()->getId());
        return $collection;
    }

    /**
     * Retrieve current invoice model instance.
     */
    public function getInvoice()
    {
        return $this->_coreRegistry->registry('current_invoice');
    }

    /**
     * Init total
     *
     * @return void
     */
    protected function _initTotals()
    {
        $this->_totals = [];
        $source = $this->getSource();
        $order = $this->getOrder();
        if (isset($source[0])) {
            $source = $source[0];
            $taxToSeller = $source['tax_to_seller'];
            $shippingAmount = $source['shipping_charges'];
            $currencyRate = $source['currency_rate'];
            $subtotal = $source['magepro_price'];
            $shippingTax = $source['shipping_tax'];
            $grandTotal = $source['magepro_price']+
                $source['shipping_charges'] +
                $source['total_tax'] -
                $source['applied_coupon_amount'] -
                $shippingTax;
            $totalTax =$source['total_tax'] - $shippingTax;
            $totalCouponAmount = $source['applied_coupon_amount'];

            $admintotaltax = 0;
            $vendortotaltax = 0;
            if ($shippingAmount > 0) {
                $totalTax = $source['total_tax'];
                $grandTotal += $shippingTax;
            }
            if (!$taxToSeller) {
                $admintotaltax = $totalTax;
            } else {
                $vendortotaltax = $totalTax;
            }

            $totalOrdered = $grandTotal;
            
            $adminSubTotal = $source['total_commission'] + $admintotaltax;

            $vendorSubTotal = $source['actual_seller_amount'] + $vendortotaltax;
            if ($source['shipping_charges']) {
                $vendorSubTotal += $shippingAmount;
            }
            $this->_totals = [];

            $this->_totals['subtotal'] = new \Magento\Framework\DataObject(
                [
                    'code' => 'subtotal',
                    'value' => $this->helper->getCurrentCurrencyPrice($currencyRate, $subtotal),
                    'label' => __('Subtotal')
                ]
            );

            $this->_totals['shipping'] = new \Magento\Framework\DataObject(
                [
                    'code' => 'shipping',
                    'value' => $this->helper->getCurrentCurrencyPrice($currencyRate, $shippingAmount),
                    'label' => __('Shipping & Handling')
                ]
            );

            $this->_totals['discount'] = new \Magento\Framework\DataObject(
                [
                    'code' => 'discount',
                    'value' => $this->helper->getCurrentCurrencyPrice($currencyRate, $totalCouponAmount),
                    'label' => __('Discount')
                ]
            );

            $this->_totals['tax'] = new \Magento\Framework\DataObject(
                [
                    'code' => 'tax',
                    'value' => $this->helper->getCurrentCurrencyPrice($currencyRate, $totalTax),
                    'label' => __('Total Tax')
                ]
            );

            $this->_totals['ordered_total'] = new \Magento\Framework\DataObject(
                [
                    'code' => 'ordered_total',
                    'strong' => 1,
                    'value' => $this->helper->getCurrentCurrencyPrice($currencyRate, $totalOrdered),
                    'label' => __('Total Ordered Amount')
                ]
            );

            if ($order->isCurrencyDifferent()) {
                $this->_totals['base_ordered_total'] = new \Magento\Framework\DataObject(
                    [
                        'code' => 'base_ordered_total',
                        'is_base' => 1,
                        'strong' => 1,
                        'value' => $totalOrdered,
                        'label' => __('Total Ordered Amount(in base currency)')
                    ]
                );
            }

            $this->_totals['vendor_total'] = new \Magento\Framework\DataObject(
                [
                    'code' => 'vendor_total',
                    'value' => $this->helper->getCurrentCurrencyPrice($currencyRate, $vendorSubTotal),
                    'label' => __('Total Vendor Amount')
                ]
            );

            if ($order->isCurrencyDifferent()) {
                $this->_totals['base_vendor_total'] = new \Magento\Framework\DataObject(
                    [
                        'code' => 'base_vendor_total',
                        'is_base' => 1,
                        'value' => $vendorSubTotal,
                        'label' => __('Total Vendor Amount(in base currency)')
                    ]
                );
            }

            $this->_totals['admin_commission'] = new \Magento\Framework\DataObject(
                [
                    'code' => 'admin_commission',
                    'value' => $this->helper->getCurrentCurrencyPrice($currencyRate, $adminSubTotal),
                    'label' => __('Total Admin Commission')
                ]
            );

            if ($order->isCurrencyDifferent()) {
                $this->_totals['base_admin_commission'] = new \Magento\Framework\DataObject(
                    [
                        'code' => 'base_admin_commission',
                        'is_base' => 1,
                        'value' => $adminSubTotal,
                        'label' => __('Total Admin Commission(in base currency)')
                    ]
                );
            }
        }
    }

    /**
     * Get label properties
     *
     * @return array
     */
    public function getLabelProperties()
    {
        $paymentCode = '';
        if ($this->_order->getPayment()) {
            $paymentCode = $this->getOrder()->getPayment()->getMethod();
        }
        if ($paymentCode == 'mpcashondelivery') {
            return 'colspan="9" class="mark"';
        }
        return 'colspan="8" class="mark"';
    }
}
