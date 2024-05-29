<?php
namespace Webkul\Helpdesk\Controller\Adminhtml\Responses\Save;

/**
 * Interceptor class for @see \Webkul\Helpdesk\Controller\Adminhtml\Responses\Save
 */
class Interceptor extends \Webkul\Helpdesk\Controller\Adminhtml\Responses\Save implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\App\Action\Context $context, \Magento\Framework\View\Result\PageFactory $resultPageFactory, \Webkul\Helpdesk\Logger\HelpdeskLogger $helpdeskLogger, \Webkul\Helpdesk\Model\ResponsesFactory $responsesFactory, \Webkul\Helpdesk\Model\ActivityRepository $activityRepository, \Magento\Backend\Model\Session $modelSession, \Magento\Backend\Model\Auth\Session $authSession, \Magento\Framework\Json\Helper\Data $jsonHelper)
    {
        $this->___init();
        parent::__construct($context, $resultPageFactory, $helpdeskLogger, $responsesFactory, $activityRepository, $modelSession, $authSession, $jsonHelper);
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
