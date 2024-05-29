<?php
namespace Webkul\Helpdesk\Controller\Adminhtml\Agentsmanagement\Agent\Save;

/**
 * Interceptor class for @see \Webkul\Helpdesk\Controller\Adminhtml\Agentsmanagement\Agent\Save
 */
class Interceptor extends \Webkul\Helpdesk\Controller\Adminhtml\Agentsmanagement\Agent\Save implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\App\Action\Context $context, \Magento\Framework\Registry $coreRegistry, \Magento\User\Model\UserFactory $userFactory, \Webkul\Helpdesk\Model\ActivityRepository $activityRepo, \Webkul\Helpdesk\Logger\HelpdeskLogger $helpdeskLogger, \Webkul\Helpdesk\Model\AgentFactory $agentFactory, \Magento\Backend\Model\Auth\Session $authSession)
    {
        $this->___init();
        parent::__construct($context, $coreRegistry, $userFactory, $activityRepo, $helpdeskLogger, $agentFactory, $authSession);
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'execute');
        return $pluginInfo ? $this->___callPlugins('execute', func_get_args(), $pluginInfo) : parent::execute();
    }

    /**
     * {@inheritdoc}
     */
    public function dispatch(\Magento\Framework\App\RequestInterface $request)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'dispatch');
        return $pluginInfo ? $this->___callPlugins('dispatch', func_get_args(), $pluginInfo) : parent::dispatch($request);
    }
}
