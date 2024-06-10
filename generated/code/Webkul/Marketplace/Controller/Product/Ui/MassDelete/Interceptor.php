<?php
namespace Webkul\Marketplace\Controller\Product\Ui\MassDelete;

/**
 * Interceptor class for @see \Webkul\Marketplace\Controller\Product\Ui\MassDelete
 */
class Interceptor extends \Webkul\Marketplace\Controller\Product\Ui\MassDelete implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\App\Action\Context $context, \Magento\Ui\Component\MassAction\Filter $filter, \Magento\Customer\Model\Session $customerSession, \Magento\Framework\Registry $coreRegistry, \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory, \Webkul\Marketplace\Model\ResourceModel\Product\CollectionFactory $sellerProductCollectionFactory, \Webkul\Marketplace\Helper\Data $helper, ?\Magento\Catalog\Api\ProductRepositoryInterface $productRepository, \Magento\Customer\Model\Url $customerUrl)
    {
        $this->___init();
        parent::__construct($context, $filter, $customerSession, $coreRegistry, $productCollectionFactory, $sellerProductCollectionFactory, $helper, $productRepository, $customerUrl);
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
    public function execute()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'execute');
        return $pluginInfo ? $this->___callPlugins('execute', func_get_args(), $pluginInfo) : parent::execute();
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
