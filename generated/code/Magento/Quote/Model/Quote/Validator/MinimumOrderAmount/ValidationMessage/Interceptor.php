<?php
namespace Magento\Quote\Model\Quote\Validator\MinimumOrderAmount\ValidationMessage;

/**
 * Interceptor class for @see \Magento\Quote\Model\Quote\Validator\MinimumOrderAmount\ValidationMessage
 */
class Interceptor extends \Magento\Quote\Model\Quote\Validator\MinimumOrderAmount\ValidationMessage implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig, \Magento\Store\Model\StoreManagerInterface $storeManager, \Magento\Framework\Locale\CurrencyInterface $currency, ?\Magento\Framework\Pricing\Helper\Data $priceHelper = null)
    {
        $this->___init();
        parent::__construct($scopeConfig, $storeManager, $currency, $priceHelper);
    }

    /**
     * {@inheritdoc}
     */
    public function getMessage()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getMessage');
        return $pluginInfo ? $this->___callPlugins('getMessage', func_get_args(), $pluginInfo) : parent::getMessage();
    }
}
