<?php
namespace Webkul\Walletsystem\Controller\Adminhtml\Wallet\MasscancelBanktransfer;

/**
 * Interceptor class for @see \Webkul\Walletsystem\Controller\Adminhtml\Wallet\MasscancelBanktransfer
 */
class Interceptor extends \Webkul\Walletsystem\Controller\Adminhtml\Wallet\MasscancelBanktransfer implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\App\Action\Context $context, \Magento\Ui\Component\MassAction\Filter $filter, \Magento\Store\Api\WebsiteRepositoryInterface $websiteRepositiry, \Webkul\Walletsystem\Model\WallettransactionFactory $transactionFactory, \Webkul\Walletsystem\Controller\Adminhtml\Wallet\Disapprove $disapprove, \Webkul\Walletsystem\Model\ResourceModel\Wallettransaction\CollectionFactory $collectionFactory, \Webkul\Walletsystem\Model\WalletUpdateData $walletUpdate, \Webkul\Walletsystem\Helper\Data $helper, \Webkul\Walletsystem\Model\WallettransactionAdditionalDataFactory $walletTransAddData)
    {
        $this->___init();
        parent::__construct($context, $filter, $websiteRepositiry, $transactionFactory, $disapprove, $collectionFactory, $walletUpdate, $helper, $walletTransAddData);
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
