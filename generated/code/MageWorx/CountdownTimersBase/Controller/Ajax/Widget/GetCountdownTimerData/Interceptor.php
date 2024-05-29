<?php
namespace MageWorx\CountdownTimersBase\Controller\Ajax\Widget\GetCountdownTimerData;

/**
 * Interceptor class for @see \MageWorx\CountdownTimersBase\Controller\Ajax\Widget\GetCountdownTimerData
 */
class Interceptor extends \MageWorx\CountdownTimersBase\Controller\Ajax\Widget\GetCountdownTimerData implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\App\Action\Context $context, \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory, \Magento\Store\Model\StoreManagerInterface $storeManager, \Magento\Customer\Model\Session $customerSession, \Psr\Log\LoggerInterface $logger, \MageWorx\CountdownTimersBase\Model\ResourceModel\CountdownTimer\CollectionFactory $collectionFactory, \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone, \MageWorx\CountdownTimersBase\Helper\TimeStamp $helperTimeStamp)
    {
        $this->___init();
        parent::__construct($context, $resultJsonFactory, $storeManager, $customerSession, $logger, $collectionFactory, $timezone, $helperTimeStamp);
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
