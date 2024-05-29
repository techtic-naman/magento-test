<?php
namespace MageWorx\OpenAI\Controller\Adminhtml\Process\MassApproveItems;

/**
 * Interceptor class for @see \MageWorx\OpenAI\Controller\Adminhtml\Process\MassApproveItems
 */
class Interceptor extends \MageWorx\OpenAI\Controller\Adminhtml\Process\MassApproveItems implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\App\Action\Context $context, \Magento\Ui\Component\MassAction\Filter $filter, \MageWorx\OpenAI\Model\ResourceModel\QueueItem\CollectionFactory $collectionFactory, \MageWorx\OpenAI\Api\QueueManagementInterface $queueManagement, \MageWorx\OpenAI\Api\QueueRepositoryInterface $queueItemRepository)
    {
        $this->___init();
        parent::__construct($context, $filter, $collectionFactory, $queueManagement, $queueItemRepository);
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
