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

    /**
     * {@inheritdoc}
     */
    public function _processUrlKeys()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, '_processUrlKeys');
        return $pluginInfo ? $this->___callPlugins('_processUrlKeys', func_get_args(), $pluginInfo) : parent::_processUrlKeys();
    }

    /**
     * {@inheritdoc}
     */
    public function getUrl($route = '', $params = [])
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getUrl');
        return $pluginInfo ? $this->___callPlugins('getUrl', func_get_args(), $pluginInfo) : parent::getUrl($route, $params);
    }

    /**
     * {@inheritdoc}
     */
    public function getActionFlag()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getActionFlag');
        return $pluginInfo ? $this->___callPlugins('getActionFlag', func_get_args(), $pluginInfo) : parent::getActionFlag();
    }

    /**
     * {@inheritdoc}
     */
    public function getRequest()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getRequest');
        return $pluginInfo ? $this->___callPlugins('getRequest', func_get_args(), $pluginInfo) : parent::getRequest();
    }

    /**
     * {@inheritdoc}
     */
    public function getResponse()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getResponse');
        return $pluginInfo ? $this->___callPlugins('getResponse', func_get_args(), $pluginInfo) : parent::getResponse();
    }
}
