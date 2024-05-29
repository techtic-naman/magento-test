<?php
namespace Webkul\Helpdesk\Controller\Adminhtml\Agentsmanagement\Group\Save;

/**
 * Interceptor class for @see \Webkul\Helpdesk\Controller\Adminhtml\Agentsmanagement\Group\Save
 */
class Interceptor extends \Webkul\Helpdesk\Controller\Adminhtml\Agentsmanagement\Group\Save implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\App\Action\Context $context, \Magento\Framework\View\Result\PageFactory $resultPageFactory, \Webkul\Helpdesk\Logger\HelpdeskLogger $helpdeskLogger, \Webkul\Helpdesk\Model\GroupFactory $groupFactory)
    {
        $this->___init();
        parent::__construct($context, $resultPageFactory, $helpdeskLogger, $groupFactory);
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
