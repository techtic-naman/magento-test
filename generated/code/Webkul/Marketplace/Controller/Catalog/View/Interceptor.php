<?php
namespace Webkul\Marketplace\Controller\Catalog\View;

/**
 * Interceptor class for @see \Webkul\Marketplace\Controller\Catalog\View
 */
class Interceptor extends \Webkul\Marketplace\Controller\Catalog\View implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\App\Action\Context $context, \Magento\Catalog\Helper\Product\View $viewHelper, \Magento\Framework\Controller\Result\ForwardFactory $resultForwardFactory, \Magento\Framework\View\Result\PageFactory $resultPageFactory, \Magento\Framework\Url\DecoderInterface $urlDecoder, ?\Magento\Framework\Stdlib\DateTime\DateTime $date = null, ?\Magento\Security\Model\Config $config = null, ?\Magento\Security\Model\AdminSessionInfoFactory $currentSession = null)
    {
        $this->___init();
        parent::__construct($context, $viewHelper, $resultForwardFactory, $resultPageFactory, $urlDecoder, $date, $config, $currentSession);
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
    public function isAdminloggedIn()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'isAdminloggedIn');
        return $pluginInfo ? $this->___callPlugins('isAdminloggedIn', func_get_args(), $pluginInfo) : parent::isAdminloggedIn();
    }

    /**
     * {@inheritdoc}
     */
    public function dispatch(\Magento\Framework\App\RequestInterface $request)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'dispatch');
        return $pluginInfo ? $this->___callPlugins('dispatch', func_get_args(), $pluginInfo) : parent::dispatch($request);
    }

    /**
     * {@inheritdoc}
     */
    public function getActionFlag()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getActionFlag');
        return $pluginInfo ? $this->___callPlugins('getActionFlag', func_get_args(), $pluginInfo) : parent::getActionFlag();
    }

    /**
     * {@inheritdoc}
     */
    public function getRequest()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getRequest');
        return $pluginInfo ? $this->___callPlugins('getRequest', func_get_args(), $pluginInfo) : parent::getRequest();
    }

    /**
     * {@inheritdoc}
     */
    public function getResponse()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getResponse');
        return $pluginInfo ? $this->___callPlugins('getResponse', func_get_args(), $pluginInfo) : parent::getResponse();
    }
}
