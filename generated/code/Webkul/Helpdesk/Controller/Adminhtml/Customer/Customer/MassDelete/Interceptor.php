<?php
namespace Webkul\Helpdesk\Controller\Adminhtml\Customer\Customer\MassDelete;

/**
 * Interceptor class for @see \Webkul\Helpdesk\Controller\Adminhtml\Customer\Customer\MassDelete
 */
class Interceptor extends \Webkul\Helpdesk\Controller\Adminhtml\Customer\Customer\MassDelete implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\App\Action\Context $context, \Magento\Ui\Component\MassAction\Filter $filter, \Webkul\Helpdesk\Model\ResourceModel\Customer\CollectionFactory $collectionFactory, \Webkul\Helpdesk\Logger\HelpdeskLogger $logger, \Webkul\Helpdesk\Model\ActivityRepository $activityRepository)
    {
        $this->___init();
        parent::__construct($context, $filter, $collectionFactory, $logger, $activityRepository);
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
