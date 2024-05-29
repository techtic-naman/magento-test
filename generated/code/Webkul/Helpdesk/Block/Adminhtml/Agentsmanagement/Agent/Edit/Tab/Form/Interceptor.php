<?php
namespace Webkul\Helpdesk\Block\Adminhtml\Agentsmanagement\Agent\Edit\Tab\Form;

/**
 * Interceptor class for @see \Webkul\Helpdesk\Block\Adminhtml\Agentsmanagement\Agent\Edit\Tab\Form
 */
class Interceptor extends \Webkul\Helpdesk\Block\Adminhtml\Agentsmanagement\Agent\Edit\Tab\Form implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\Block\Template\Context $context, \Magento\Framework\Registry $registry, \Magento\Framework\Data\FormFactory $formFactory, \Magento\Backend\Model\Auth\Session $adminSession, \Webkul\Helpdesk\Model\AgentLevelFactory $agentLevelFactory, \Webkul\Helpdesk\Model\GroupFactory $groupFactory, \Magento\Config\Model\Config\Source\Locale\Timezone $timezone, array $data = [])
    {
        $this->___init();
        parent::__construct($context, $registry, $formFactory, $adminSession, $agentLevelFactory, $groupFactory, $timezone, $data);
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
