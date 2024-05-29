<?php
namespace Webkul\Helpdesk\Controller\Ticket\View;

/**
 * Interceptor class for @see \Webkul\Helpdesk\Controller\Ticket\View
 */
class Interceptor extends \Webkul\Helpdesk\Controller\Ticket\View implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\App\Action\Context $context, \Magento\Framework\View\Result\PageFactory $resultPageFactory, \Webkul\Helpdesk\Helper\Tickets $ticketHelper, \Webkul\Helpdesk\Model\TicketsFactory $ticketsFactory)
    {
        $this->___init();
        parent::__construct($context, $resultPageFactory, $ticketHelper, $ticketsFactory);
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
