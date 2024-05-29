<?php
namespace Meetanshi\Bulksms\Controller\Adminhtml\Campaign\Massdelete;

/**
 * Interceptor class for @see \Meetanshi\Bulksms\Controller\Adminhtml\Campaign\Massdelete
 */
class Interceptor extends \Meetanshi\Bulksms\Controller\Adminhtml\Campaign\Massdelete implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\App\Action\Context $context, \Magento\Framework\View\Result\PageFactory $resultPageFactory, \Magento\Ui\Component\MassAction\Filter $filter, \Meetanshi\Bulksms\Model\CampaignFactory $bulksms, \Meetanshi\Bulksms\Model\ResourceModel\Campaign\CollectionFactory $collectionFactory)
    {
        $this->___init();
        parent::__construct($context, $resultPageFactory, $filter, $bulksms, $collectionFactory);
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
