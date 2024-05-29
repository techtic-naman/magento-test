<?php
namespace Webkul\Helpdesk\Controller\Adminhtml\SupportCenter\Edit;

/**
 * Interceptor class for @see \Webkul\Helpdesk\Controller\Adminhtml\SupportCenter\Edit
 */
class Interceptor extends \Webkul\Helpdesk\Controller\Adminhtml\SupportCenter\Edit implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\App\Action\Context $context, \Magento\Framework\View\Result\PageFactory $resultPageFactory, \Magento\Framework\Registry $registry, \Webkul\Helpdesk\Model\SupportCenterFactory $supportCenterFactory)
    {
        $this->___init();
        parent::__construct($context, $resultPageFactory, $registry, $supportCenterFactory);
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
