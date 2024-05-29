<?php
namespace Webkul\Helpdesk\Block\Adminhtml\Businesshours\Edit\Tab\HoursTemplate;

/**
 * Interceptor class for @see \Webkul\Helpdesk\Block\Adminhtml\Businesshours\Edit\Tab\HoursTemplate
 */
class Interceptor extends \Webkul\Helpdesk\Block\Adminhtml\Businesshours\Edit\Tab\HoursTemplate implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\Block\Template\Context $context, \Webkul\Helpdesk\Model\BusinesshoursFactory $businesshoursFactory, \Webkul\Helpdesk\Model\Source\Days $daysModel, \Webkul\Helpdesk\Model\Source\TimeInterval $timeIntervalModel, \Magento\Framework\Serialize\SerializerInterface $serializer, \Magento\Framework\Json\Helper\Data $jsonHelper, array $data = [])
    {
        $this->___init();
        parent::__construct($context, $businesshoursFactory, $daysModel, $timeIntervalModel, $serializer, $jsonHelper, $data);
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
