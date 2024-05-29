<?php
namespace MageWorx\RewardPoints\Controller\Checkout\Apply;

/**
 * Interceptor class for @see \MageWorx\RewardPoints\Controller\Checkout\Apply
 */
class Interceptor extends \MageWorx\RewardPoints\Controller\Checkout\Apply implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\App\Action\Context $context, \Magento\Quote\Api\CartRepositoryInterface $quoteRepository, \Magento\Checkout\Model\Session $checkoutSession, \MageWorx\RewardPoints\Model\QuoteTriggerSetter $quoteTriggerSetter, \MageWorx\RewardPoints\Helper\Data $helperData, \Magento\Framework\Serialize\SerializerInterface $serializer, \Psr\Log\LoggerInterface $logger)
    {
        $this->___init();
        parent::__construct($context, $quoteRepository, $checkoutSession, $quoteTriggerSetter, $helperData, $serializer, $logger);
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
