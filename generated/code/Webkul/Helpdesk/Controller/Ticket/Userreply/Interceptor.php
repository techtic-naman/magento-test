<?php
namespace Webkul\Helpdesk\Controller\Ticket\Userreply;

/**
 * Interceptor class for @see \Webkul\Helpdesk\Controller\Ticket\Userreply
 */
class Interceptor extends \Webkul\Helpdesk\Controller\Ticket\Userreply implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\App\Action\Context $context, \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator, \Magento\Framework\View\Result\PageFactory $resultPageFactory, \Webkul\Helpdesk\Model\ActivityRepository $activityRepo, \Webkul\Helpdesk\Model\EventsRepository $eventsRepo, \Webkul\Helpdesk\Model\TicketsFactory $ticketsFactory, \Webkul\Helpdesk\Model\ThreadRepository $threadRepo, \Webkul\Helpdesk\Helper\Tickets $ticketsHelper, \Webkul\Helpdesk\Logger\HelpdeskLogger $helpdeskLogger, \Webkul\Helpdesk\Model\TicketdraftFactory $ticketdraftFactory, \Webkul\Helpdesk\Model\AttachmentRepository $attachmentRepo, \Webkul\Helpdesk\Model\ThreadFactory $threadFactory, \Webkul\Helpdesk\Helper\Data $helper, \Magento\User\Model\UserFactory $userFactory)
    {
        $this->___init();
        parent::__construct($context, $formKeyValidator, $resultPageFactory, $activityRepo, $eventsRepo, $ticketsFactory, $threadRepo, $ticketsHelper, $helpdeskLogger, $ticketdraftFactory, $attachmentRepo, $threadFactory, $helper, $userFactory);
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
