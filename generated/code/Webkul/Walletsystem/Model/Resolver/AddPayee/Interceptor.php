<?php
namespace Webkul\Walletsystem\Model\Resolver\AddPayee;

/**
 * Interceptor class for @see \Webkul\Walletsystem\Model\Resolver\AddPayee
 */
class Interceptor extends \Webkul\Walletsystem\Model\Resolver\AddPayee implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Customer\Model\CustomerFactory $customerFactory, \Webkul\Walletsystem\Helper\Data $walletHelper, \Webkul\Walletsystem\Model\WalletPayeeFactory $walletPayee, \Magento\Store\Model\StoreManagerInterface $storeManager, \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig, \Webkul\Walletsystem\Model\WalletNotification $walletNotification)
    {
        $this->___init();
        parent::__construct($customerFactory, $walletHelper, $walletPayee, $storeManager, $scopeConfig, $walletNotification);
    }

    /**
     * {@inheritdoc}
     */
    public function resolve(\Magento\Framework\GraphQl\Config\Element\Field $field, $context, \Magento\Framework\GraphQl\Schema\Type\ResolveInfo $info, ?array $value = null, ?array $args = null)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'resolve');
        return $pluginInfo ? $this->___callPlugins('resolve', func_get_args(), $pluginInfo) : parent::resolve($field, $context, $info, $value, $args);
    }
}
