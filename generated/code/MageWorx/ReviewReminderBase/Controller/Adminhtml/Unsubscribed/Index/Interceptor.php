<?php
namespace MageWorx\ReviewReminderBase\Controller\Adminhtml\Unsubscribed\Index;

/**
 * Interceptor class for @see \MageWorx\ReviewReminderBase\Controller\Adminhtml\Unsubscribed\Index
 */
class Interceptor extends \MageWorx\ReviewReminderBase\Controller\Adminhtml\Unsubscribed\Index implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\App\Action\Context $context, \MageWorx\ReviewReminderBase\Api\UnsubscribedRepositoryInterface $unsubscribedRepository, \Magento\Framework\View\Result\PageFactory $resultPageFactory)
    {
        $this->___init();
        parent::__construct($context, $unsubscribedRepository, $resultPageFactory);
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
