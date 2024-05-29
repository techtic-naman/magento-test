<?php
namespace Webkul\Walletsystem\Rewrite\CustomerData\Cart;

/**
 * Interceptor class for @see \Webkul\Walletsystem\Rewrite\CustomerData\Cart
 */
class Interceptor extends \Webkul\Walletsystem\Rewrite\CustomerData\Cart implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Checkout\Model\Session $checkoutSession, \Magento\Catalog\Model\ResourceModel\Url $catalogUrl, \Magento\Checkout\Model\Cart $checkoutCart, \Magento\Checkout\Helper\Data $checkoutHelper, \Magento\Checkout\CustomerData\ItemPoolInterface $itemPoolInterface, \Magento\Framework\View\LayoutInterface $layout, \Webkul\Walletsystem\Helper\Data $walletHelper, array $data = [])
    {
        $this->___init();
        parent::__construct($checkoutSession, $catalogUrl, $checkoutCart, $checkoutHelper, $itemPoolInterface, $layout, $walletHelper, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function getSectionData()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getSectionData');
        return $pluginInfo ? $this->___callPlugins('getSectionData', func_get_args(), $pluginInfo) : parent::getSectionData();
    }
}
