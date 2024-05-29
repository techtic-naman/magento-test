<?php
namespace Magento\Multishipping\Block\Checkout\Overview;

/**
 * Interceptor class for @see \Magento\Multishipping\Block\Checkout\Overview
 */
class Interceptor extends \Magento\Multishipping\Block\Checkout\Overview implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\View\Element\Template\Context $context, \Magento\Multishipping\Model\Checkout\Type\Multishipping $multishipping, \Magento\Tax\Helper\Data $taxHelper, \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency, \Magento\Quote\Model\Quote\TotalsCollector $totalsCollector, \Magento\Quote\Model\Quote\TotalsReader $totalsReader, array $data = [], ?\Magento\Checkout\Helper\Data $checkoutHelper = null)
    {
        $this->___init();
        parent::__construct($context, $multishipping, $taxHelper, $priceCurrency, $totalsCollector, $totalsReader, $data, $checkoutHelper);
    }

    /**
     * {@inheritdoc}
     */
    public function getPaymentHtml()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getPaymentHtml');
        return $pluginInfo ? $this->___callPlugins('getPaymentHtml', func_get_args(), $pluginInfo) : parent::getPaymentHtml();
    }

    /**
     * {@inheritdoc}
     */
    public function getShippingAddressTotals($address)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getShippingAddressTotals');
        return $pluginInfo ? $this->___callPlugins('getShippingAddressTotals', func_get_args(), $pluginInfo) : parent::getShippingAddressTotals($address);
    }

    /**
     * {@inheritdoc}
     */
    public function getTotal()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getTotal');
        return $pluginInfo ? $this->___callPlugins('getTotal', func_get_args(), $pluginInfo) : parent::getTotal();
    }
}
