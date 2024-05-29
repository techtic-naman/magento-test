<?php
namespace MageWorx\RewardPoints\Controller\Checkout\Cancel;

/**
 * Interceptor class for @see \MageWorx\RewardPoints\Controller\Checkout\Cancel
 */
class Interceptor extends \MageWorx\RewardPoints\Controller\Checkout\Cancel implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\App\Action\Context $context, \MageWorx\RewardPoints\Model\QuoteTriggerSetter $quoteTriggerSetter, \Magento\Checkout\Model\Session $checkoutSession, \Magento\Quote\Api\CartRepositoryInterface $quoteRepository, \Magento\Framework\Serialize\SerializerInterface $serializer, \Psr\Log\LoggerInterface $logger)
    {
        $this->___init();
        parent::__construct($context, $quoteTriggerSetter, $checkoutSession, $quoteRepository, $serializer, $logger);
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
