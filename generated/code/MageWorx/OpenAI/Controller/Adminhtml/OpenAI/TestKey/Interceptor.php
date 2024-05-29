<?php
namespace MageWorx\OpenAI\Controller\Adminhtml\OpenAI\TestKey;

/**
 * Interceptor class for @see \MageWorx\OpenAI\Controller\Adminhtml\OpenAI\TestKey
 */
class Interceptor extends \MageWorx\OpenAI\Controller\Adminhtml\OpenAI\TestKey implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\App\Action\Context $context, \MageWorx\OpenAI\Api\MessengerInterface $messenger, \MageWorx\OpenAI\Api\OptionsInterfaceFactory $optionsFactory, \MageWorx\OpenAI\Helper\Data $helper)
    {
        $this->___init();
        parent::__construct($context, $messenger, $optionsFactory, $helper);
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
