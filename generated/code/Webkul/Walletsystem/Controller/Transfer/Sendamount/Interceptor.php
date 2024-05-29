<?php
namespace Webkul\Walletsystem\Controller\Transfer\Sendamount;

/**
 * Interceptor class for @see \Webkul\Walletsystem\Controller\Transfer\Sendamount
 */
class Interceptor extends \Webkul\Walletsystem\Controller\Transfer\Sendamount implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\App\Action\Context $context, \Magento\Framework\View\Result\PageFactory $resultPageFactory, \Webkul\Walletsystem\Helper\Data $walletHelper, \Webkul\Walletsystem\Helper\Mail $walletMail, \Webkul\Walletsystem\Model\WalletTransferData $walletSession, \Magento\Framework\Encryption\EncryptorInterface $encryptor, \Webkul\Walletsystem\Model\WalletUpdateData $walletUpdate)
    {
        $this->___init();
        parent::__construct($context, $resultPageFactory, $walletHelper, $walletMail, $walletSession, $encryptor, $walletUpdate);
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
