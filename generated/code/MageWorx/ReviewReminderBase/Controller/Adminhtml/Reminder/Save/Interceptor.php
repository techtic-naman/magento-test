<?php
namespace MageWorx\ReviewReminderBase\Controller\Adminhtml\Reminder\Save;

/**
 * Interceptor class for @see \MageWorx\ReviewReminderBase\Controller\Adminhtml\Reminder\Save
 */
class Interceptor extends \MageWorx\ReviewReminderBase\Controller\Adminhtml\Reminder\Save implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\App\Action\Context $context, \MageWorx\ReviewReminderBase\Api\ReminderRepositoryInterface $reminderRepository, \Magento\Framework\View\Result\PageFactory $resultPageFactory, \MageWorx\ReviewReminderBase\Api\Data\ReminderInterfaceFactory $reminderFactory, \Magento\Framework\Reflection\DataObjectProcessor $dataObjectProcessor, \Magento\Framework\Api\DataObjectHelper $dataObjectHelper, \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor)
    {
        $this->___init();
        parent::__construct($context, $reminderRepository, $resultPageFactory, $reminderFactory, $dataObjectProcessor, $dataObjectHelper, $dataPersistor);
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
