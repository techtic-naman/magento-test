<?php
namespace Webkul\Walletsystem\Controller\Adminhtml\Wallet\Bankdetails;

/**
 * Interceptor class for @see \Webkul\Walletsystem\Controller\Adminhtml\Wallet\Bankdetails
 */
class Interceptor extends \Webkul\Walletsystem\Controller\Adminhtml\Wallet\Bankdetails implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\App\Action\Context $context, \Magento\Customer\Model\CustomerFactory $customerModel, \Webkul\Walletsystem\Model\WalletNotification $notification, \Webkul\Walletsystem\Helper\Data $helper)
    {
        $this->___init();
        parent::__construct($context, $customerModel, $notification, $helper);
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
