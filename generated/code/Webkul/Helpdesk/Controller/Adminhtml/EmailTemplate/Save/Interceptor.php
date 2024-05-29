<?php
namespace Webkul\Helpdesk\Controller\Adminhtml\EmailTemplate\Save;

/**
 * Interceptor class for @see \Webkul\Helpdesk\Controller\Adminhtml\EmailTemplate\Save
 */
class Interceptor extends \Webkul\Helpdesk\Controller\Adminhtml\EmailTemplate\Save implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\App\Action\Context $context, \Magento\Framework\View\Result\PageFactory $resultPageFactory, \Magento\Backend\Model\Auth\Session $authSession, \Webkul\Helpdesk\Model\EmailTemplateFactory $emailtemplateFactory, \Magento\Backend\Model\Session $modelSession, \Webkul\Helpdesk\Model\ActivityRepository $activityRepo, \Webkul\Helpdesk\Logger\HelpdeskLogger $helpdeskLogger, \Magento\Email\Model\BackendTemplate $emailbackendTemp, \Magento\Framework\Stdlib\DateTime\DateTime $date)
    {
        $this->___init();
        parent::__construct($context, $resultPageFactory, $authSession, $emailtemplateFactory, $modelSession, $activityRepo, $helpdeskLogger, $emailbackendTemp, $date);
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
