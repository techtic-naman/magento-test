<?php
namespace Webkul\Helpdesk\Controller\Adminhtml\ConnectEmail\Fetchmails;

/**
 * Interceptor class for @see \Webkul\Helpdesk\Controller\Adminhtml\ConnectEmail\Fetchmails
 */
class Interceptor extends \Webkul\Helpdesk\Controller\Adminhtml\ConnectEmail\Fetchmails implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\App\Action\Context $context, \Magento\Framework\View\Result\PageFactory $resultPageFactory, \Webkul\Helpdesk\Model\ConnectEmailFactory $connectEmailFactory, \Magento\Backend\Model\Session $modelSession, \Webkul\Helpdesk\Model\ActivityRepository $activityRepo, \Webkul\Helpdesk\Logger\HelpdeskLogger $helpdeskLogger, \Webkul\Helpdesk\Model\MailfetchRepository $mailfetchRepo)
    {
        $this->___init();
        parent::__construct($context, $resultPageFactory, $connectEmailFactory, $modelSession, $activityRepo, $helpdeskLogger, $mailfetchRepo);
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
