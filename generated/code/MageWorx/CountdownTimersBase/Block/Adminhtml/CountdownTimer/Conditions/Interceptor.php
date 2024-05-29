<?php
namespace MageWorx\CountdownTimersBase\Block\Adminhtml\CountdownTimer\Conditions;

/**
 * Interceptor class for @see \MageWorx\CountdownTimersBase\Block\Adminhtml\CountdownTimer\Conditions
 */
class Interceptor extends \MageWorx\CountdownTimersBase\Block\Adminhtml\CountdownTimer\Conditions implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\Block\Template\Context $context, \Magento\Framework\Registry $registry, \Magento\Framework\Data\FormFactory $formFactory, \MageWorx\CountdownTimersBase\Api\CountdownTimerRepositoryInterface $countdownTimerRepository, \Magento\Rule\Block\Conditions $conditions, \MageWorx\CountdownTimersBase\Helper\CountdownTimer\Rules $rulesHelper, array $data = [])
    {
        $this->___init();
        parent::__construct($context, $registry, $formFactory, $countdownTimerRepository, $conditions, $rulesHelper, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function getForm()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getForm');
        return $pluginInfo ? $this->___callPlugins('getForm', func_get_args(), $pluginInfo) : parent::getForm();
    }
}
