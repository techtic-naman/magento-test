<?php
namespace Webkul\Walletsystem\Controller\Transfer\RequestDelete;

/**
 * Interceptor class for @see \Webkul\Walletsystem\Controller\Transfer\RequestDelete
 */
class Interceptor extends \Webkul\Walletsystem\Controller\Transfer\RequestDelete implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\App\Action\Context $context, \Webkul\Walletsystem\Model\AccountDetails $accountDetails, \Magento\Framework\App\Response\RedirectInterface $redirect)
    {
        $this->___init();
        parent::__construct($context, $accountDetails, $redirect);
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
