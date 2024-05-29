<?php
namespace Webkul\Walletsystem\Model\Resolver\AdminCustomerAccountDetails;

/**
 * Interceptor class for @see \Webkul\Walletsystem\Model\Resolver\AdminCustomerAccountDetails
 */
class Interceptor extends \Webkul\Walletsystem\Model\Resolver\AdminCustomerAccountDetails implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Customer\Model\CustomerFactory $customerFactory, \Webkul\Walletsystem\Model\ResourceModel\AccountDetails\CollectionFactory $accountResourceModel)
    {
        $this->___init();
        parent::__construct($customerFactory, $accountResourceModel);
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
