<?php
namespace Webkul\Helpdesk\Controller\Adminhtml\Ticketsmanagement\Tickets\Adminreply;

/**
 * Interceptor class for @see \Webkul\Helpdesk\Controller\Adminhtml\Ticketsmanagement\Tickets\Adminreply
 */
class Interceptor extends \Webkul\Helpdesk\Controller\Adminhtml\Ticketsmanagement\Tickets\Adminreply implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\App\Action\Context $context, \Magento\Framework\View\Result\PageFactory $resultPageFactory, \Webkul\Helpdesk\Model\TicketsRepository $ticketsRepo, \Magento\Backend\Model\Auth\Session $authSession, \Magento\User\Model\UserFactory $userFactory, \Webkul\Helpdesk\Model\ActivityRepository $activityRepo, \Webkul\Helpdesk\Logger\HelpdeskLogger $helpdeskLogger, \Webkul\Helpdesk\Model\ThreadRepository $threadRepo, \Webkul\Helpdesk\Model\EventsRepository $eventsRepo, \Webkul\Helpdesk\Model\AttachmentRepository $attachmentRepo, \Webkul\Helpdesk\Model\TicketdraftFactory $ticketdraftFactory, \Webkul\Helpdesk\Model\ThreadFactory $threadFactory, \Webkul\Helpdesk\Model\SlaRepository $slaRepo, \Webkul\Helpdesk\Model\TicketsFactory $ticketFactory, \Webkul\Helpdesk\Helper\Data $helper)
    {
        $this->___init();
        parent::__construct($context, $resultPageFactory, $ticketsRepo, $authSession, $userFactory, $activityRepo, $helpdeskLogger, $threadRepo, $eventsRepo, $attachmentRepo, $ticketdraftFactory, $threadFactory, $slaRepo, $ticketFactory, $helper);
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
