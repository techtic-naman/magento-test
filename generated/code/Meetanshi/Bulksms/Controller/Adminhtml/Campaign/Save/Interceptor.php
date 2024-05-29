<?php
namespace Meetanshi\Bulksms\Controller\Adminhtml\Campaign\Save;

/**
 * Interceptor class for @see \Meetanshi\Bulksms\Controller\Adminhtml\Campaign\Save
 */
class Interceptor extends \Meetanshi\Bulksms\Controller\Adminhtml\Campaign\Save implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\App\Action\Context $context, \Magento\Framework\View\Result\PageFactory $resultPageFactory, \Meetanshi\Bulksms\Model\CampaignFactory $bulksms, \Magento\Backend\Model\Session $session)
    {
        $this->___init();
        parent::__construct($context, $resultPageFactory, $bulksms, $session);
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
