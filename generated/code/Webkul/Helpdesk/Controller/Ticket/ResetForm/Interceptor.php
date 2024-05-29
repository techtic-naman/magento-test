<?php
namespace Webkul\Helpdesk\Controller\Ticket\ResetForm;

/**
 * Interceptor class for @see \Webkul\Helpdesk\Controller\Ticket\ResetForm
 */
class Interceptor extends \Webkul\Helpdesk\Controller\Ticket\ResetForm implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\App\Action\Context $context, \Magento\Framework\View\Result\PageFactory $resultPageFactory, \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor, \Webkul\Helpdesk\Model\ResourceModel\Ticketdraft\CollectionFactory $draftCollFactory, \Magento\Customer\Model\Session $customerSession, \Webkul\Helpdesk\Helper\Tickets $ticketsHelper)
    {
        $this->___init();
        parent::__construct($context, $resultPageFactory, $dataPersistor, $draftCollFactory, $customerSession, $ticketsHelper);
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
