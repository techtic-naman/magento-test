<?php
namespace Webkul\Walletsystem\Controller\Transfer\Payeeupdate;

/**
 * Interceptor class for @see \Webkul\Walletsystem\Controller\Transfer\Payeeupdate
 */
class Interceptor extends \Webkul\Walletsystem\Controller\Transfer\Payeeupdate implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\App\Action\Context $context, \Magento\Framework\View\Result\PageFactory $resultPageFactory, \Webkul\Walletsystem\Helper\Data $walletHelper, \Webkul\Walletsystem\Model\WalletUpdateData $walletUpdate, \Magento\Customer\Model\CustomerFactory $customerModel, \Magento\Store\Model\StoreManagerInterface $storeManager, \Magento\Framework\Json\Helper\Data $jsonHelper, \Webkul\Walletsystem\Model\WalletPayeeFactory $walletPayee, \Magento\Customer\Model\Session $customerSession)
    {
        $this->___init();
        parent::__construct($context, $resultPageFactory, $walletHelper, $walletUpdate, $customerModel, $storeManager, $jsonHelper, $walletPayee, $customerSession);
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
