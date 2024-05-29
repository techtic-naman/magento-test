<?php
namespace Meetanshi\OrderTracking\Controller\OrderTracking\Track;

/**
 * Interceptor class for @see \Meetanshi\OrderTracking\Controller\OrderTracking\Track
 */
class Interceptor extends \Meetanshi\OrderTracking\Controller\OrderTracking\Track implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\App\Action\Context $context, \Magento\Framework\View\Result\PageFactory $resultPageFactory, \Magento\Framework\Registry $registry, \Magento\Sales\Model\OrderFactory $orderFactory, \Magento\Framework\View\LayoutFactory $layoutFactory, \Meetanshi\OrderTracking\Helper\Data $helper)
    {
        $this->___init();
        parent::__construct($context, $resultPageFactory, $registry, $orderFactory, $layoutFactory, $helper);
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
