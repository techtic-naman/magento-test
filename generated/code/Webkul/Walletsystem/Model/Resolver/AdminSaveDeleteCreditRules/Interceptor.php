<?php
namespace Webkul\Walletsystem\Model\Resolver\AdminSaveDeleteCreditRules;

/**
 * Interceptor class for @see \Webkul\Walletsystem\Model\Resolver\AdminSaveDeleteCreditRules
 */
class Interceptor extends \Webkul\Walletsystem\Model\Resolver\AdminSaveDeleteCreditRules implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Webkul\Walletsystem\Model\WalletcreditrulesFactory $walletcreditrulesFactory, \Magento\Framework\Stdlib\DateTime\DateTime $date, \Webkul\Walletsystem\Api\WalletCreditRepositoryInterface $creditRuleRepository, \Psr\Log\LoggerInterface $logger)
    {
        $this->___init();
        parent::__construct($walletcreditrulesFactory, $date, $creditRuleRepository, $logger);
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
