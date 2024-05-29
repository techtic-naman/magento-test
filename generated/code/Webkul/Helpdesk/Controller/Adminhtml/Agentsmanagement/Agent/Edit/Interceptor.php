<?php
namespace Webkul\Helpdesk\Controller\Adminhtml\Agentsmanagement\Agent\Edit;

/**
 * Interceptor class for @see \Webkul\Helpdesk\Controller\Adminhtml\Agentsmanagement\Agent\Edit
 */
class Interceptor extends \Webkul\Helpdesk\Controller\Adminhtml\Agentsmanagement\Agent\Edit implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\App\Action\Context $context, \Magento\Framework\View\Result\PageFactory $resultPageFactory, \Magento\Framework\Registry $registry, \Webkul\Helpdesk\Model\AgentFactory $agentFactory, \Magento\User\Model\UserFactory $userFactory)
    {
        $this->___init();
        parent::__construct($context, $resultPageFactory, $registry, $agentFactory, $userFactory);
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
