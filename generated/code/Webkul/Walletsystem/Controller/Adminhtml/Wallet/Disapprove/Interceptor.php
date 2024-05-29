<?php
namespace Webkul\Walletsystem\Controller\Adminhtml\Wallet\Disapprove;

/**
 * Interceptor class for @see \Webkul\Walletsystem\Controller\Adminhtml\Wallet\Disapprove
 */
class Interceptor extends \Webkul\Walletsystem\Controller\Adminhtml\Wallet\Disapprove implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\App\Action\Context $context, \Webkul\Walletsystem\Model\WalletrecordFactory $walletrecord, \Webkul\Walletsystem\Model\WallettransactionFactory $transactionFactory, \Magento\Store\Api\WebsiteRepositoryInterface $websiteRepositiry, \Webkul\Walletsystem\Helper\Data $walletHelper, \Magento\Framework\Stdlib\DateTime\DateTime $date, \Webkul\Walletsystem\Helper\Mail $mailHelper, \Webkul\Walletsystem\Model\WalletUpdateData $walletUpdate, \Webkul\Walletsystem\Model\WallettransactionAdditionalDataFactory $walletTransAddData)
    {
        $this->___init();
        parent::__construct($context, $walletrecord, $transactionFactory, $websiteRepositiry, $walletHelper, $date, $mailHelper, $walletUpdate, $walletTransAddData);
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
