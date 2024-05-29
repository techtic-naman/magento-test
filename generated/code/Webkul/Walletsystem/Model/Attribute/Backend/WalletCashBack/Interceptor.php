<?php
namespace Webkul\Walletsystem\Model\Attribute\Backend\WalletCashBack;

/**
 * Interceptor class for @see \Webkul\Walletsystem\Model\Attribute\Backend\WalletCashBack
 */
class Interceptor extends \Webkul\Walletsystem\Model\Attribute\Backend\WalletCashBack implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Catalog\Model\ProductFactory $productFactory)
    {
        $this->___init();
        parent::__construct($productFactory);
    }

    /**
     * {@inheritdoc}
     */
    public function validate($object)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'validate');
        return $pluginInfo ? $this->___callPlugins('validate', func_get_args(), $pluginInfo) : parent::validate($object);
    }
}
