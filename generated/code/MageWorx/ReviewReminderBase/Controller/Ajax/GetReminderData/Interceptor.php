<?php
namespace MageWorx\ReviewReminderBase\Controller\Ajax\GetReminderData;

/**
 * Interceptor class for @see \MageWorx\ReviewReminderBase\Controller\Ajax\GetReminderData
 */
class Interceptor extends \MageWorx\ReviewReminderBase\Controller\Ajax\GetReminderData implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\App\Action\Context $context, \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory, \Magento\Store\Model\StoreManagerInterface $storeManager, \Magento\Customer\Model\Session $customerSession, \Psr\Log\LoggerInterface $logger, \MageWorx\ReviewReminderBase\Model\Content\DataProvider\PopupDataProvider $popupDataProvider, \Magento\Framework\Encryption\EncryptorInterface $encryptor)
    {
        $this->___init();
        parent::__construct($context, $resultJsonFactory, $storeManager, $customerSession, $logger, $popupDataProvider, $encryptor);
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

    /**
     * {@inheritdoc}
     */
    public function getActionFlag()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getActionFlag');
        return $pluginInfo ? $this->___callPlugins('getActionFlag', func_get_args(), $pluginInfo) : parent::getActionFlag();
    }

    /**
     * {@inheritdoc}
     */
    public function getRequest()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getRequest');
        return $pluginInfo ? $this->___callPlugins('getRequest', func_get_args(), $pluginInfo) : parent::getRequest();
    }

    /**
     * {@inheritdoc}
     */
    public function getResponse()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getResponse');
        return $pluginInfo ? $this->___callPlugins('getResponse', func_get_args(), $pluginInfo) : parent::getResponse();
    }
}
