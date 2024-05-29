<?php
namespace Webkul\Helpdesk\Controller\Ticket\Loginpost;

/**
 * Interceptor class for @see \Webkul\Helpdesk\Controller\Ticket\Loginpost
 */
class Interceptor extends \Webkul\Helpdesk\Controller\Ticket\Loginpost implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\App\Action\Context $context, \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator, \Webkul\Helpdesk\Helper\Tickets $ticketsHelper, \Webkul\Helpdesk\Model\TicketsFactory $ticketsFactory, \Webkul\Helpdesk\Logger\HelpdeskLogger $helpdeskLogger, \Magento\Framework\Session\SessionManager $coreSession)
    {
        $this->___init();
        parent::__construct($context, $formKeyValidator, $ticketsHelper, $ticketsFactory, $helpdeskLogger, $coreSession);
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
