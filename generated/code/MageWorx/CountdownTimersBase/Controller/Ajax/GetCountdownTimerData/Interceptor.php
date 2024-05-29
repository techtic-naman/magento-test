<?php
namespace MageWorx\CountdownTimersBase\Controller\Ajax\GetCountdownTimerData;

/**
 * Interceptor class for @see \MageWorx\CountdownTimersBase\Controller\Ajax\GetCountdownTimerData
 */
class Interceptor extends \MageWorx\CountdownTimersBase\Controller\Ajax\GetCountdownTimerData implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\App\Action\Context $context, \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory, \MageWorx\CountdownTimersBase\Api\FrontendCountdownTimerResolverInterface $frontendCountdownTimerResolver, \Magento\Store\Model\StoreManagerInterface $storeManager, \Magento\Customer\Model\Session $customerSession, \Psr\Log\LoggerInterface $logger)
    {
        $this->___init();
        parent::__construct($context, $resultJsonFactory, $frontendCountdownTimerResolver, $storeManager, $customerSession, $logger);
    }

    /**
     * {@inheritdoc}
     */
    public function execute() : ?\Magento\Framework\Controller\Result\Json
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
