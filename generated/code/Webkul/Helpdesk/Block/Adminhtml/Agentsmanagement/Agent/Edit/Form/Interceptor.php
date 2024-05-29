<?php
namespace Webkul\Helpdesk\Block\Adminhtml\Agentsmanagement\Agent\Edit\Form;

/**
 * Interceptor class for @see \Webkul\Helpdesk\Block\Adminhtml\Agentsmanagement\Agent\Edit\Form
 */
class Interceptor extends \Webkul\Helpdesk\Block\Adminhtml\Agentsmanagement\Agent\Edit\Form implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\Block\Template\Context $context, \Magento\Framework\Registry $registry, \Magento\Framework\Data\FormFactory $formFactory, \Magento\Store\Model\System\Store $systemStore, \Webkul\Helpdesk\Model\AgentFactory $agentFactory, array $data = [])
    {
        $this->___init();
        parent::__construct($context, $registry, $formFactory, $systemStore, $agentFactory, $data);
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
