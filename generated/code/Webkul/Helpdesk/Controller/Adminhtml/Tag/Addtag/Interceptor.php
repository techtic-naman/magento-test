<?php
namespace Webkul\Helpdesk\Controller\Adminhtml\Tag\Addtag;

/**
 * Interceptor class for @see \Webkul\Helpdesk\Controller\Adminhtml\Tag\Addtag
 */
class Interceptor extends \Webkul\Helpdesk\Controller\Adminhtml\Tag\Addtag implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\App\Action\Context $context, \Magento\Framework\View\Result\PageFactory $resultPageFactory, \Webkul\Helpdesk\Model\TagFactory $tagFactory, \Webkul\Helpdesk\Logger\HelpdeskLogger $helpdeskLogger)
    {
        $this->___init();
        parent::__construct($context, $resultPageFactory, $tagFactory, $helpdeskLogger);
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
