<?php
namespace Webkul\Helpdesk\Controller\Adminhtml\Ticketsmanagement\Tickets\Changeproperty;

/**
 * Interceptor class for @see \Webkul\Helpdesk\Controller\Adminhtml\Ticketsmanagement\Tickets\Changeproperty
 */
class Interceptor extends \Webkul\Helpdesk\Controller\Adminhtml\Ticketsmanagement\Tickets\Changeproperty implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\App\Action\Context $context, \Magento\Framework\View\Result\PageFactory $resultPageFactory, \Webkul\Helpdesk\Model\TicketsFactory $ticketsFactory, \Webkul\Helpdesk\Model\EventsRepository $eventsRepo, \Webkul\Helpdesk\Logger\HelpdeskLogger $helpdeskLogger, \Webkul\Helpdesk\Model\TicketslaRepository $ticketslaRepo, \Magento\Framework\Serialize\SerializerInterface $serializer, \Webkul\Helpdesk\Model\SlaRepository $slaRepo, \Webkul\Helpdesk\Helper\Tickets $ticketHelper, \Webkul\Helpdesk\Model\ResourceModel\Ticketsla\CollectionFactory $ticketSlaCollFactory, \Webkul\Helpdesk\Model\ResourceModel\Slapolicy\CollectionFactory $slapolicyCollFactory)
    {
        $this->___init();
        parent::__construct($context, $resultPageFactory, $ticketsFactory, $eventsRepo, $helpdeskLogger, $ticketslaRepo, $serializer, $slaRepo, $ticketHelper, $ticketSlaCollFactory, $slapolicyCollFactory);
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
