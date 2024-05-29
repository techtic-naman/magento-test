<?php
namespace Webkul\Walletsystem\Controller\Adminhtml\Creditrules\Save;

/**
 * Interceptor class for @see \Webkul\Walletsystem\Controller\Adminhtml\Creditrules\Save
 */
class Interceptor extends \Webkul\Walletsystem\Controller\Adminhtml\Creditrules\Save implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\App\Action\Context $context, \Webkul\Walletsystem\Model\WalletcreditrulesFactory $walletcreditrulesFactory, \Magento\Framework\Stdlib\DateTime\DateTime $date, \Webkul\Walletsystem\Helper\Data $helper)
    {
        $this->___init();
        parent::__construct($context, $walletcreditrulesFactory, $date, $helper);
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
