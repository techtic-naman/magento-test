<?php
namespace Webkul\Helpdesk\Controller\Adminhtml\SupportCenter\MassStatus;

/**
 * Interceptor class for @see \Webkul\Helpdesk\Controller\Adminhtml\SupportCenter\MassStatus
 */
class Interceptor extends \Webkul\Helpdesk\Controller\Adminhtml\SupportCenter\MassStatus implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\App\Action\Context $context, \Magento\Ui\Component\MassAction\Filter $filter, \Webkul\Helpdesk\Model\ResourceModel\SupportCenter\CollectionFactory $collectionFactory, \Webkul\Helpdesk\Logger\HelpdeskLogger $logger, \Webkul\Helpdesk\Helper\Data $helper)
    {
        $this->___init();
        parent::__construct($context, $filter, $collectionFactory, $logger, $helper);
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
