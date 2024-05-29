<?php
namespace MageWorx\CountdownTimersBase\Controller\Adminhtml\CountdownTimer\MassDelete;

/**
 * Interceptor class for @see \MageWorx\CountdownTimersBase\Controller\Adminhtml\CountdownTimer\MassDelete
 */
class Interceptor extends \MageWorx\CountdownTimersBase\Controller\Adminhtml\CountdownTimer\MassDelete implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\App\Action\Context $context, \Magento\Framework\Controller\ResultFactory $resultFactory, \Psr\Log\LoggerInterface $logger, \MageWorx\CountdownTimersBase\Api\CountdownTimerRepositoryInterface $countdownTimerRepository, \Magento\Ui\Component\MassAction\Filter $filter, \MageWorx\CountdownTimersBase\Model\ResourceModel\CountdownTimer\CollectionFactory $collectionFactory, \Magento\Framework\App\ResourceConnection $resourceConnection, $successMessage, $errorMessage)
    {
        $this->___init();
        parent::__construct($context, $resultFactory, $logger, $countdownTimerRepository, $filter, $collectionFactory, $resourceConnection, $successMessage, $errorMessage);
    }

    /**
     * {@inheritdoc}
     */
    public function execute() : \Magento\Framework\Controller\Result\Redirect
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
