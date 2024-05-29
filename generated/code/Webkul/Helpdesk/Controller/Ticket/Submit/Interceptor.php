<?php
namespace Webkul\Helpdesk\Controller\Ticket\Submit;

/**
 * Interceptor class for @see \Webkul\Helpdesk\Controller\Ticket\Submit
 */
class Interceptor extends \Webkul\Helpdesk\Controller\Ticket\Submit implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\App\Action\Context $context, \Magento\Customer\Model\Session $customerSession, \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator, \Webkul\Helpdesk\Helper\Tickets $ticketsHelper, \Webkul\Helpdesk\Helper\Data $helper, \Webkul\Helpdesk\Model\TicketsRepository $ticketsRepo, \Webkul\Helpdesk\Model\TicketsFactory $ticketsFactory, \Webkul\Helpdesk\Model\CustomerFactory $customerFactory, \Webkul\Helpdesk\Model\ActivityRepository $activityRepo, \Webkul\Helpdesk\Model\ThreadRepository $threadRepo, \Webkul\Helpdesk\Model\AttachmentRepository $attachmentRepo, \Webkul\Helpdesk\Model\ThreadFactory $threadFactory, \Webkul\Helpdesk\Logger\HelpdeskLogger $helpdeskLogger, \Magento\Customer\Model\CustomerFactory $customerDataFactory, \Magento\Framework\Message\ManagerInterface $messageManager, \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor, \Magento\Framework\Escaper $_escaper, \Webkul\Helpdesk\Model\EventsRepository $eventsRepo, \Webkul\Helpdesk\Model\ResourceModel\Ticketdraft\CollectionFactory $draftCollFactory)
    {
        $this->___init();
        parent::__construct($context, $customerSession, $formKeyValidator, $ticketsHelper, $helper, $ticketsRepo, $ticketsFactory, $customerFactory, $activityRepo, $threadRepo, $attachmentRepo, $threadFactory, $helpdeskLogger, $customerDataFactory, $messageManager, $dataPersistor, $_escaper, $eventsRepo, $draftCollFactory);
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
