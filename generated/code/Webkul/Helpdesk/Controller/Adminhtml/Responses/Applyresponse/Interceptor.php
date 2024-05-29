<?php
namespace Webkul\Helpdesk\Controller\Adminhtml\Responses\Applyresponse;

/**
 * Interceptor class for @see \Webkul\Helpdesk\Controller\Adminhtml\Responses\Applyresponse
 */
class Interceptor extends \Webkul\Helpdesk\Controller\Adminhtml\Responses\Applyresponse implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\App\Action\Context $context, \Magento\Framework\View\Result\PageFactory $resultPageFactory, \Webkul\Helpdesk\Model\ResponsesFactory $responseFactory, \Webkul\Helpdesk\Model\ResponsesRepository $responseRepo)
    {
        $this->___init();
        parent::__construct($context, $resultPageFactory, $responseFactory, $responseRepo);
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
