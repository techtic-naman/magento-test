<?php
namespace Webkul\Helpdesk\Controller\Adminhtml\SlaPolicy\Save;

/**
 * Interceptor class for @see \Webkul\Helpdesk\Controller\Adminhtml\SlaPolicy\Save
 */
class Interceptor extends \Webkul\Helpdesk\Controller\Adminhtml\SlaPolicy\Save implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\App\Action\Context $context, \Magento\Framework\View\Result\PageFactory $resultPageFactory, \Magento\Backend\Model\Auth\Session $authSession, \Webkul\Helpdesk\Model\SlapolicyFactory $slapolicyFactory, \Magento\Backend\Model\Session $modelSession, \Webkul\Helpdesk\Logger\HelpdeskLogger $helpdeskLogger, \Magento\Framework\Serialize\SerializerInterface $serializer, \Webkul\Helpdesk\Model\ResourceModel\SlapolicyFactory $resSlaPolicyFactory)
    {
        $this->___init();
        parent::__construct($context, $resultPageFactory, $authSession, $slapolicyFactory, $modelSession, $helpdeskLogger, $serializer, $resSlaPolicyFactory);
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
