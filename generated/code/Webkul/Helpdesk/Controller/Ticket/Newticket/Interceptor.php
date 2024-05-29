<?php
namespace Webkul\Helpdesk\Controller\Ticket\Newticket;

/**
 * Interceptor class for @see \Webkul\Helpdesk\Controller\Ticket\Newticket
 */
class Interceptor extends \Webkul\Helpdesk\Controller\Ticket\Newticket implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\App\Action\Context $context, \Magento\Framework\View\Result\PageFactory $resultPageFactory, \Webkul\Helpdesk\Helper\Data $dataHelper, \Webkul\Helpdesk\Helper\Tickets $ticketHelper, \Webkul\Helpdesk\Model\CustomerFactory $helpdeskCustomerFactory, \Magento\Customer\Model\Session $mageCustomerSession, \Magento\Framework\Url\EncoderInterface $urlEncoder, \Magento\Framework\UrlInterface $urlInterface)
    {
        $this->___init();
        parent::__construct($context, $resultPageFactory, $dataHelper, $ticketHelper, $helpdeskCustomerFactory, $mageCustomerSession, $urlEncoder, $urlInterface);
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