<?php
namespace Amasty\Storelocator\Block\Adminhtml\Attribute\Edit\Tab\Main;

/**
 * Interceptor class for @see \Amasty\Storelocator\Block\Adminhtml\Attribute\Edit\Tab\Main
 */
class Interceptor extends \Amasty\Storelocator\Block\Adminhtml\Attribute\Edit\Tab\Main implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\Block\Template\Context $context, \Magento\Framework\Registry $registry, \Magento\Framework\Data\FormFactory $formFactory, array $data = [], ?\Amasty\Storelocator\Model\Config\Source\AttributeStatuses $attributeStatuses = null)
    {
        $this->___init();
        parent::__construct($context, $registry, $formFactory, $data, $attributeStatuses);
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
