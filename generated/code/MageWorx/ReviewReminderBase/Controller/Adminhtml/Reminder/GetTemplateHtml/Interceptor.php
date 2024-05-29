<?php
namespace MageWorx\ReviewReminderBase\Controller\Adminhtml\Reminder\GetTemplateHtml;

/**
 * Interceptor class for @see \MageWorx\ReviewReminderBase\Controller\Adminhtml\Reminder\GetTemplateHtml
 */
class Interceptor extends \MageWorx\ReviewReminderBase\Controller\Adminhtml\Reminder\GetTemplateHtml implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\App\Action\Context $context, \Magento\Framework\View\Result\PageFactory $resultPageFactory, \MageWorx\ReviewReminderBase\Api\ReminderRepositoryInterface $reminderRepository, \MageWorx\ReviewReminderBase\Model\ResourceModel\Reminder\Template\CollectionFactory $collectionFactory)
    {
        $this->___init();
        parent::__construct($context, $resultPageFactory, $reminderRepository, $collectionFactory);
    }

    /**
     * {@inheritdoc}
     */
    public function execute() : \Magento\Framework\Controller\ResultInterface
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