<?php
namespace Webkul\Walletsystem\Controller\Transfer\Bankamount;

/**
 * Interceptor class for @see \Webkul\Walletsystem\Controller\Transfer\Bankamount
 */
class Interceptor extends \Webkul\Walletsystem\Controller\Transfer\Bankamount implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\App\Action\Context $context, \Magento\Framework\View\Result\PageFactory $resultPageFactory, \Webkul\Walletsystem\Helper\Data $walletHelper, \Webkul\Walletsystem\Model\WalletNotification $walletNotification, \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig, \Webkul\Walletsystem\Model\WalletUpdateData $walletUpdate)
    {
        $this->___init();
        parent::__construct($context, $resultPageFactory, $walletHelper, $walletNotification, $scopeConfig, $walletUpdate);
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
