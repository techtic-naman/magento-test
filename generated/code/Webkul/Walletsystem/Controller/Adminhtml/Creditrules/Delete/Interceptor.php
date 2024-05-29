<?php
namespace Webkul\Walletsystem\Controller\Adminhtml\Creditrules\Delete;

/**
 * Interceptor class for @see \Webkul\Walletsystem\Controller\Adminhtml\Creditrules\Delete
 */
class Interceptor extends \Webkul\Walletsystem\Controller\Adminhtml\Creditrules\Delete implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\App\Action\Context $context, \Webkul\Walletsystem\Api\WalletCreditRepositoryInterface $creditRuleRepository)
    {
        $this->___init();
        parent::__construct($context, $creditRuleRepository);
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
