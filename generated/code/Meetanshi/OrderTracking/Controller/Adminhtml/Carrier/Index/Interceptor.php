<?php
namespace Meetanshi\OrderTracking\Controller\Adminhtml\Carrier\Index;

/**
 * Interceptor class for @see \Meetanshi\OrderTracking\Controller\Adminhtml\Carrier\Index
 */
class Interceptor extends \Meetanshi\OrderTracking\Controller\Adminhtml\Carrier\Index implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\Registry $registry, \Magento\Backend\App\Action\Context $context, \Magento\Backend\Model\Auth\Session $backendSession, \Meetanshi\OrderTracking\Model\CarrierFactory $carrierFactory, \Magento\Framework\View\Result\PageFactory $resultPageFactory, \Meetanshi\OrderTracking\Helper\Data $helper, \Meetanshi\OrderTracking\Model\ResourceModel\Carrier\CollectionFactory $collectionFactory, \Magento\Ui\Component\MassAction\Filter $filter)
    {
        $this->___init();
        parent::__construct($registry, $context, $backendSession, $carrierFactory, $resultPageFactory, $helper, $collectionFactory, $filter);
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
