<?php
namespace Webkul\Helpdesk\Block\Adminhtml\Slapolicy\Edit\Tab\ConditionTemplate;

/**
 * Interceptor class for @see \Webkul\Helpdesk\Block\Adminhtml\Slapolicy\Edit\Tab\ConditionTemplate
 */
class Interceptor extends \Webkul\Helpdesk\Block\Adminhtml\Slapolicy\Edit\Tab\ConditionTemplate implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\Block\Template\Context $context, \Webkul\Helpdesk\Model\TicketsPriorityFactory $ticketsPriorityFactory, \Webkul\Helpdesk\Model\TypeFactory $typeFactory, \Webkul\Helpdesk\Model\GroupFactory $groupFactory, \Webkul\Helpdesk\Model\AgentFactory $agentFactory, \Webkul\Helpdesk\Model\TicketsStatusFactory $ticketsStatusFactory, \Webkul\Helpdesk\Model\EmailTemplateFactory $emailTemplateFactory, \Webkul\Helpdesk\Model\TagFactory $tagFactory, \Webkul\Helpdesk\Model\ResponsesFactory $responsesFactory, \Webkul\Helpdesk\Model\EventsFactory $eventsFactory, \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig, \Webkul\Helpdesk\Model\SlapolicyFactory $slapolicyFactory, \Magento\User\Model\UserFactory $userFactory, \Webkul\Helpdesk\Helper\Data $dataHelper, \Magento\Framework\Json\Helper\Data $jsonHelper, \Magento\Framework\Serialize\SerializerInterface $serializer, array $data = [])
    {
        $this->___init();
        parent::__construct($context, $ticketsPriorityFactory, $typeFactory, $groupFactory, $agentFactory, $ticketsStatusFactory, $emailTemplateFactory, $tagFactory, $responsesFactory, $eventsFactory, $wysiwygConfig, $slapolicyFactory, $userFactory, $dataHelper, $jsonHelper, $serializer, $data);
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
