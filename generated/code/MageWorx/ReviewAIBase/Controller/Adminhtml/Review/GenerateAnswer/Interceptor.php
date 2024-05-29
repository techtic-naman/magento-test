<?php
namespace MageWorx\ReviewAIBase\Controller\Adminhtml\Review\GenerateAnswer;

/**
 * Interceptor class for @see \MageWorx\ReviewAIBase\Controller\Adminhtml\Review\GenerateAnswer
 */
class Interceptor extends \MageWorx\ReviewAIBase\Controller\Adminhtml\Review\GenerateAnswer implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\App\Action\Context $context, \Magento\Framework\Controller\ResultFactory $resultFactory, \MageWorx\ReviewAIBase\Api\ReviewAnswerGeneratorInterface $reviewAnswerGenerator)
    {
        $this->___init();
        parent::__construct($context, $resultFactory, $reviewAnswerGenerator);
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
