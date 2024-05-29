<?php
namespace MageWorx\ReviewAIBase\Controller\Adminhtml\ReviewSummary\GenerateForProduct;

/**
 * Interceptor class for @see \MageWorx\ReviewAIBase\Controller\Adminhtml\ReviewSummary\GenerateForProduct
 */
class Interceptor extends \MageWorx\ReviewAIBase\Controller\Adminhtml\ReviewSummary\GenerateForProduct implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\App\Action\Context $context, \MageWorx\ReviewAIBase\Api\ReviewSummaryGeneratorInterface $reviewSummaryGenerator, \Magento\Framework\Serialize\Serializer\Json $jsonSerializer, \Magento\Store\Model\App\Emulation $appEmulation, \Magento\Store\Model\StoreManagerInterface $storeManager, \Psr\Log\LoggerInterface $logger)
    {
        $this->___init();
        parent::__construct($context, $reviewSummaryGenerator, $jsonSerializer, $appEmulation, $storeManager, $logger);
    }

    /**
     * {@inheritdoc}
     */
    public function execute() : \Magento\Framework\Controller\ResultInterface
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
