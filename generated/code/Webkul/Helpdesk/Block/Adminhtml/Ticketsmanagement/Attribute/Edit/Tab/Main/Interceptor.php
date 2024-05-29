<?php
namespace Webkul\Helpdesk\Block\Adminhtml\Ticketsmanagement\Attribute\Edit\Tab\Main;

/**
 * Interceptor class for @see \Webkul\Helpdesk\Block\Adminhtml\Ticketsmanagement\Attribute\Edit\Tab\Main
 */
class Interceptor extends \Webkul\Helpdesk\Block\Adminhtml\Ticketsmanagement\Attribute\Edit\Tab\Main implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Eav\Helper\Data $eavData, \Magento\Config\Model\Config\Source\YesnoFactory $yesnoFactory, \Magento\Eav\Model\Adminhtml\System\Config\Source\InputtypeFactory $inputTypeFactory, \Magento\Eav\Block\Adminhtml\Attribute\PropertyLocker $propertyLocker, \Magento\Backend\Block\Template\Context $context, \Magento\Framework\Registry $registry, \Magento\Framework\Data\FormFactory $formFactory, \Magento\Config\Model\Config\Source\Yesno $yesNo, \Webkul\Helpdesk\Model\TypeFactory $typeFactory, \Magento\Eav\Model\Entity $eavEntity, array $data = [])
    {
        $this->___init();
        parent::__construct($eavData, $yesnoFactory, $inputTypeFactory, $propertyLocker, $context, $registry, $formFactory, $yesNo, $typeFactory, $eavEntity, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function getForm()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getForm');
        return $pluginInfo ? $this->___callPlugins('getForm', func_get_args(), $pluginInfo) : parent::getForm();
    }
}
