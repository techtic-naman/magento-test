<?php
namespace MageWorx\ReviewAIBase\Controller\Adminhtml\ReviewSummary\AddToQueueMassAction;

/**
 * Interceptor class for @see \MageWorx\ReviewAIBase\Controller\Adminhtml\ReviewSummary\AddToQueueMassAction
 */
class Interceptor extends \MageWorx\ReviewAIBase\Controller\Adminhtml\ReviewSummary\AddToQueueMassAction implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\App\Action\Context $context, \Magento\Ui\Component\MassAction\Filter $filter, \MageWorx\ReviewAIBase\Model\ResourceModel\ReviewSummary\Grid\CollectionFactory $gridCollectionFactory, \MageWorx\ReviewAIBase\Model\ResourceModel\ReviewSummary $reviewSummaryResource, \MageWorx\ReviewAIBase\Api\ReviewSummaryGeneratorInterface $reviewSummaryGenerator)
    {
        $this->___init();
        parent::__construct($context, $filter, $gridCollectionFactory, $reviewSummaryResource, $reviewSummaryGenerator);
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
