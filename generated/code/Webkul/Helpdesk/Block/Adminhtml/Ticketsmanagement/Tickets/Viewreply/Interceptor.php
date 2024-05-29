<?php
namespace Webkul\Helpdesk\Block\Adminhtml\Ticketsmanagement\Tickets\Viewreply;

/**
 * Interceptor class for @see \Webkul\Helpdesk\Block\Adminhtml\Ticketsmanagement\Tickets\Viewreply
 */
class Interceptor extends \Webkul\Helpdesk\Block\Adminhtml\Ticketsmanagement\Tickets\Viewreply implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\Block\Template\Context $context, \Webkul\Helpdesk\Model\TicketsPriorityFactory $ticketsPriorityFactory, \Webkul\Helpdesk\Model\TypeFactory $typeFactory, \Webkul\Helpdesk\Model\GroupFactory $groupFactory, \Webkul\Helpdesk\Model\AgentFactory $agentFactory, \Webkul\Helpdesk\Model\TicketsStatusFactory $ticketsStatusFactory, \Webkul\Helpdesk\Model\EmailTemplateFactory $emailTemplateFactory, \Webkul\Helpdesk\Model\TagFactory $tagFactory, \Webkul\Helpdesk\Model\ResponsesFactory $responsesFactory, \Webkul\Helpdesk\Model\EventsFactory $eventsFactory, \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig, \Webkul\Helpdesk\Model\SlapolicyFactory $slapolicyFactory, \Magento\User\Model\UserFactory $userFactory, \Webkul\Helpdesk\Model\TicketsFactory $ticketsFactory, \Webkul\Helpdesk\Model\TicketnotesFactory $ticketnotesFactory, \Magento\Catalog\Model\ResourceModel\Eav\Attribute $eavAttribute, \Webkul\Helpdesk\Model\ThreadFactory $threadFactory, \Magento\Framework\View\Asset\Repository $assetRepo, \Webkul\Helpdesk\Helper\Data $dataHelper, \Webkul\Helpdesk\Helper\Tickets $ticketHelper, \Magento\Framework\Json\Helper\Data $jsonHelper, \Magento\Framework\Serialize\SerializerInterface $serializer, \Magento\Framework\Filesystem\Driver\File $filesystemFile, \Webkul\Helpdesk\Model\TicketsCustomAttributesFactory $ticketsCusAttrFactory, array $data = [])
    {
        $this->___init();
        parent::__construct($context, $ticketsPriorityFactory, $typeFactory, $groupFactory, $agentFactory, $ticketsStatusFactory, $emailTemplateFactory, $tagFactory, $responsesFactory, $eventsFactory, $wysiwygConfig, $slapolicyFactory, $userFactory, $ticketsFactory, $ticketnotesFactory, $eavAttribute, $threadFactory, $assetRepo, $dataHelper, $ticketHelper, $jsonHelper, $serializer, $filesystemFile, $ticketsCusAttrFactory, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function render(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'render');
        return $pluginInfo ? $this->___callPlugins('render', func_get_args(), $pluginInfo) : parent::render($element);
    }
}
