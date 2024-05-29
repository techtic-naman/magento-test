<?php
namespace Webkul\Walletsystem\Controller\Adminhtml\Wallet\MassupdateBanktransfer;

/**
 * Interceptor class for @see \Webkul\Walletsystem\Controller\Adminhtml\Wallet\MassupdateBanktransfer
 */
class Interceptor extends \Webkul\Walletsystem\Controller\Adminhtml\Wallet\MassupdateBanktransfer implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\App\Action\Context $context, \Webkul\Walletsystem\Helper\Mail $mailHelper, \Magento\Ui\Component\MassAction\Filter $filter, \Webkul\Walletsystem\Model\ResourceModel\Wallettransaction\CollectionFactory $collectionFactory, \Webkul\Walletsystem\Model\WallettransactionFactory $transactionFactory)
    {
        $this->___init();
        parent::__construct($context, $mailHelper, $filter, $collectionFactory, $transactionFactory);
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
