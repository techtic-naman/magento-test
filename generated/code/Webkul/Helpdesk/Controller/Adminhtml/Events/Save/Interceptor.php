<?php
namespace Webkul\Helpdesk\Controller\Adminhtml\Events\Save;

/**
 * Interceptor class for @see \Webkul\Helpdesk\Controller\Adminhtml\Events\Save
 */
class Interceptor extends \Webkul\Helpdesk\Controller\Adminhtml\Events\Save implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\App\Action\Context $context, \Magento\Framework\View\Result\PageFactory $resultPageFactory, \Magento\Backend\Model\Auth\Session $authSession, \Webkul\Helpdesk\Model\EventsFactory $eventsFactory, \Magento\Backend\Model\Session $modelSession, \Magento\Framework\Json\Helper\Data $jsonHelper, \Webkul\Helpdesk\Model\ActivityRepository $activityRepo, \Webkul\Helpdesk\Logger\HelpdeskLogger $helpdeskLogger, \Magento\Email\Model\BackendTemplateFactory $emailbackendTemp, \Magento\Framework\Stdlib\DateTime\DateTime $date)
    {
        $this->___init();
        parent::__construct($context, $resultPageFactory, $authSession, $eventsFactory, $modelSession, $jsonHelper, $activityRepo, $helpdeskLogger, $emailbackendTemp, $date);
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
