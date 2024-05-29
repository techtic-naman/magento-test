<?php
namespace Meetanshi\Bulksms\Controller\Adminhtml\Campaign\Delete;

/**
 * Interceptor class for @see \Meetanshi\Bulksms\Controller\Adminhtml\Campaign\Delete
 */
class Interceptor extends \Meetanshi\Bulksms\Controller\Adminhtml\Campaign\Delete implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\App\Action\Context $context, \Magento\Framework\View\Result\PageFactory $resultPageFactory, \Meetanshi\Bulksms\Model\CampaignFactory $bulksms)
    {
        $this->___init();
        parent::__construct($context, $resultPageFactory, $bulksms);
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
