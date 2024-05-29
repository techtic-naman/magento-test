<?php
namespace Webkul\Helpdesk\Controller\Ticket\Saveindraft;

/**
 * Interceptor class for @see \Webkul\Helpdesk\Controller\Ticket\Saveindraft
 */
class Interceptor extends \Webkul\Helpdesk\Controller\Ticket\Saveindraft implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\App\Action\Context $context, \Webkul\Helpdesk\Model\TicketdraftFactory $ticketdraftFactory, \Webkul\Helpdesk\Logger\HelpdeskLogger $helpdeskLogger, \Webkul\Helpdesk\Helper\Tickets $ticketsHelper)
    {
        $this->___init();
        parent::__construct($context, $ticketdraftFactory, $helpdeskLogger, $ticketsHelper);
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
