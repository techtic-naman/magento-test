<?php
namespace Meetanshi\OrderTracking\Controller\OrderTracking\View;

/**
 * Interceptor class for @see \Meetanshi\OrderTracking\Controller\OrderTracking\View
 */
class Interceptor extends \Meetanshi\OrderTracking\Controller\OrderTracking\View implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\App\Action\Context $context, \Magento\Framework\View\Result\PageFactory $resultPageFactory, \Magento\Framework\Registry $registry, \Magento\Framework\View\LayoutFactory $layoutFactory, \Magento\Sales\Model\OrderFactory $orderFactory, \Magento\Framework\UrlInterface $urlInterface)
    {
        $this->___init();
        parent::__construct($context, $resultPageFactory, $registry, $layoutFactory, $orderFactory, $urlInterface);
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
