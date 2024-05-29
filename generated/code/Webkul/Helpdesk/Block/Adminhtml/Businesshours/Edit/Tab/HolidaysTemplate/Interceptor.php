<?php
namespace Webkul\Helpdesk\Block\Adminhtml\Businesshours\Edit\Tab\HolidaysTemplate;

/**
 * Interceptor class for @see \Webkul\Helpdesk\Block\Adminhtml\Businesshours\Edit\Tab\HolidaysTemplate
 */
class Interceptor extends \Webkul\Helpdesk\Block\Adminhtml\Businesshours\Edit\Tab\HolidaysTemplate implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\Block\Template\Context $context, \Webkul\Helpdesk\Model\BusinesshoursFactory $businesshoursFactory, \Webkul\Helpdesk\Model\Source\Months $monthsModel, \Magento\Framework\Serialize\SerializerInterface $serializer, array $data = [])
    {
        $this->___init();
        parent::__construct($context, $businesshoursFactory, $monthsModel, $serializer, $data);
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
