<?php
namespace Magento\SalesRule\Model\RulesApplier;

/**
 * Interceptor class for @see \Magento\SalesRule\Model\RulesApplier
 */
class Interceptor extends \Magento\SalesRule\Model\RulesApplier implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\SalesRule\Model\Rule\Action\Discount\CalculatorFactory $calculatorFactory, \Magento\Framework\Event\ManagerInterface $eventManager, \Magento\SalesRule\Model\Utility $utility, ?\Magento\SalesRule\Model\Quote\ChildrenValidationLocator $childrenValidationLocator = null, ?\Magento\SalesRule\Model\Rule\Action\Discount\DataFactory $discountDataFactory = null, ?\Magento\SalesRule\Api\Data\RuleDiscountInterfaceFactory $discountInterfaceFactory = null, ?\Magento\SalesRule\Api\Data\DiscountDataInterfaceFactory $discountDataInterfaceFactory = null)
    {
        $this->___init();
        parent::__construct($calculatorFactory, $eventManager, $utility, $childrenValidationLocator, $discountDataFactory, $discountInterfaceFactory, $discountDataInterfaceFactory);
    }

    /**
     * {@inheritdoc}
     */
    public function applyRules($item, $rules, $skipValidation, $couponCode)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'applyRules');
        return $pluginInfo ? $this->___callPlugins('applyRules', func_get_args(), $pluginInfo) : parent::applyRules($item, $rules, $skipValidation, $couponCode);
    }
}
