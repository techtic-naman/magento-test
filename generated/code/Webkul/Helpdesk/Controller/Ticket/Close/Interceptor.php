<?php
namespace Webkul\Helpdesk\Controller\Ticket\Close;

/**
 * Interceptor class for @see \Webkul\Helpdesk\Controller\Ticket\Close
 */
class Interceptor extends \Webkul\Helpdesk\Controller\Ticket\Close implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\App\Action\Context $context, \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator, \Magento\Framework\View\Result\PageFactory $resultPageFactory, \Webkul\Helpdesk\Model\EventsRepository $eventsRepo, \Webkul\Helpdesk\Model\TicketsFactory $ticketsFactory, \Webkul\Helpdesk\Helper\Tickets $ticketsHelper, \Webkul\Helpdesk\Helper\Data $helper, \Webkul\Helpdesk\Logger\HelpdeskLogger $helpdeskLogger)
    {
        $this->___init();
        parent::__construct($context, $formKeyValidator, $resultPageFactory, $eventsRepo, $ticketsFactory, $ticketsHelper, $helper, $helpdeskLogger);
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
