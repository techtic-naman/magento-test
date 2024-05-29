<?php
namespace MageWorx\ReviewAIBase\Controller\Adminhtml\ReviewSummary\DisableGenerationMassAction;

/**
 * Interceptor class for @see \MageWorx\ReviewAIBase\Controller\Adminhtml\ReviewSummary\DisableGenerationMassAction
 */
class Interceptor extends \MageWorx\ReviewAIBase\Controller\Adminhtml\ReviewSummary\DisableGenerationMassAction implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\App\Action\Context $context, \Magento\Ui\Component\MassAction\Filter $filter, \MageWorx\ReviewAIBase\Model\ResourceModel\ReviewSummary\Grid\CollectionFactory $gridCollectionFactory, \MageWorx\ReviewAIBase\Model\ResourceModel\ReviewSummary\CollectionFactory $reviewSummaryCollectionFactory, \MageWorx\ReviewAIBase\Api\ReviewSummarySaverInterface $reviewSummarySaver)
    {
        $this->___init();
        parent::__construct($context, $filter, $gridCollectionFactory, $reviewSummaryCollectionFactory, $reviewSummarySaver);
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