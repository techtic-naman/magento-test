<?php
namespace MageWorx\CountdownTimersBase\Controller\Adminhtml\CountdownTimer\NewConditionHtml;

/**
 * Interceptor class for @see \MageWorx\CountdownTimersBase\Controller\Adminhtml\CountdownTimer\NewConditionHtml
 */
class Interceptor extends \MageWorx\CountdownTimersBase\Controller\Adminhtml\CountdownTimer\NewConditionHtml implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\App\Action\Context $context, \Magento\Framework\Controller\ResultFactory $resultFactory, \Psr\Log\LoggerInterface $logger, \MageWorx\CountdownTimersBase\Api\CountdownTimerRepositoryInterface $countdownTimerRepository, \MageWorx\CountdownTimersBase\Helper\CountdownTimer\Rules $rulesHelper)
    {
        $this->___init();
        parent::__construct($context, $resultFactory, $logger, $countdownTimerRepository, $rulesHelper);
    }

    /**
     * {@inheritdoc}
     */
    public function execute() : void
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'execute');
        $pluginInfo ? $this->___callPlugins('execute', func_get_args(), $pluginInfo) : parent::execute();
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
