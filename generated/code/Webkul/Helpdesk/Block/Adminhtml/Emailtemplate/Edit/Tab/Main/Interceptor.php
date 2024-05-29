<?php
namespace Webkul\Helpdesk\Block\Adminhtml\Emailtemplate\Edit\Tab\Main;

/**
 * Interceptor class for @see \Webkul\Helpdesk\Block\Adminhtml\Emailtemplate\Edit\Tab\Main
 */
class Interceptor extends \Webkul\Helpdesk\Block\Adminhtml\Emailtemplate\Edit\Tab\Main implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\Block\Template\Context $context, \Magento\Framework\Registry $registry, \Magento\Framework\Data\FormFactory $formFactory, \Magento\Variable\Model\VariableFactory $variableFactory, \Magento\Variable\Model\Source\Variables $variables, array $data = [], ?\Magento\Framework\Serialize\Serializer\Json $serializer = null)
    {
        $this->___init();
        parent::__construct($context, $registry, $formFactory, $variableFactory, $variables, $data, $serializer);
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
