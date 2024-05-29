<?php
namespace Webkul\Walletsystem\Controller\Index\Applypaymentamount;

/**
 * Interceptor class for @see \Webkul\Walletsystem\Controller\Index\Applypaymentamount
 */
class Interceptor extends \Webkul\Walletsystem\Controller\Index\Applypaymentamount implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\App\Action\Context $context, \Webkul\Walletsystem\Helper\Data $helper, \Magento\Checkout\Model\Session $checkoutSession, \Magento\Framework\View\Result\PageFactory $resultPageFactory, \Magento\Checkout\Helper\Cart $cartHelper)
    {
        $this->___init();
        parent::__construct($context, $helper, $checkoutSession, $resultPageFactory, $cartHelper);
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
