<?php
namespace MageWorx\ReviewReminderBase\Controller\Adminhtml\Reminder\Index;

/**
 * Interceptor class for @see \MageWorx\ReviewReminderBase\Controller\Adminhtml\Reminder\Index
 */
class Interceptor extends \MageWorx\ReviewReminderBase\Controller\Adminhtml\Reminder\Index implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\App\Action\Context $context, \MageWorx\ReviewReminderBase\Api\ReminderRepositoryInterface $reminderRepository, \Magento\Framework\View\Result\PageFactory $resultPageFactory)
    {
        $this->___init();
        parent::__construct($context, $reminderRepository, $resultPageFactory);
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
