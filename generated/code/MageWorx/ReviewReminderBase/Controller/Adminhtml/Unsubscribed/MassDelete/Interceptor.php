<?php
namespace MageWorx\ReviewReminderBase\Controller\Adminhtml\Unsubscribed\MassDelete;

/**
 * Interceptor class for @see \MageWorx\ReviewReminderBase\Controller\Adminhtml\Unsubscribed\MassDelete
 */
class Interceptor extends \MageWorx\ReviewReminderBase\Controller\Adminhtml\Unsubscribed\MassDelete implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\App\Action\Context $context, \MageWorx\ReviewReminderBase\Api\UnsubscribedRepositoryInterface $unsubscribedRepository, \Magento\Ui\Component\MassAction\Filter $filter, \MageWorx\ReviewReminderBase\Model\ResourceModel\Unsubscribed\CollectionFactory $collectionFactory, $successMessage, $errorMessage)
    {
        $this->___init();
        parent::__construct($context, $unsubscribedRepository, $filter, $collectionFactory, $successMessage, $errorMessage);
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
