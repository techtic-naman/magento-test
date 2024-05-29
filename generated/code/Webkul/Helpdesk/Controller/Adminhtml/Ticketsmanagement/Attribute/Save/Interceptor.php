<?php
namespace Webkul\Helpdesk\Controller\Adminhtml\Ticketsmanagement\Attribute\Save;

/**
 * Interceptor class for @see \Webkul\Helpdesk\Controller\Adminhtml\Ticketsmanagement\Attribute\Save
 */
class Interceptor extends \Webkul\Helpdesk\Controller\Adminhtml\Ticketsmanagement\Attribute\Save implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\App\Action\Context $context, \Magento\Framework\View\Result\PageFactory $resultPageFactory, \Webkul\Helpdesk\Logger\HelpdeskLogger $helpdeskLogger, \Webkul\Helpdesk\Model\Eav\CustomAttributeFactory $eavCustomAttrFactory, \Webkul\Helpdesk\Model\ActivityRepository $activityRepository, \Magento\Eav\Model\Entity $eavEntity, \Magento\Eav\Model\Entity\Attribute $eavEntityAttr, \Webkul\Helpdesk\Model\TicketsCustomAttributesFactory $ticketCustomAttrFactory)
    {
        $this->___init();
        parent::__construct($context, $resultPageFactory, $helpdeskLogger, $eavCustomAttrFactory, $activityRepository, $eavEntity, $eavEntityAttr, $ticketCustomAttrFactory);
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
