<?php
namespace Webkul\Walletsystem\Controller\Adminhtml\Wallet\Massaddamount;

/**
 * Interceptor class for @see \Webkul\Walletsystem\Controller\Adminhtml\Wallet\Massaddamount
 */
class Interceptor extends \Webkul\Walletsystem\Controller\Adminhtml\Wallet\Massaddamount implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\App\Action\Context $context, \Webkul\Walletsystem\Model\WalletrecordFactory $walletrecord, \Webkul\Walletsystem\Model\WallettransactionFactory $transactionFactory, \Webkul\Walletsystem\Helper\Data $walletHelper, \Magento\Framework\Stdlib\DateTime\DateTime $date, \Webkul\Walletsystem\Helper\Mail $mailHelper, \Webkul\Walletsystem\Model\WalletUpdateData $walletUpdate, \Magento\Framework\Json\DecoderInterface $jsonDecoder)
    {
        $this->___init();
        parent::__construct($context, $walletrecord, $transactionFactory, $walletHelper, $date, $mailHelper, $walletUpdate, $jsonDecoder);
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