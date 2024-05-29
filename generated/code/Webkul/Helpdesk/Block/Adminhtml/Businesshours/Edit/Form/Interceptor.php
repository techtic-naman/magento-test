<?php
namespace Webkul\Helpdesk\Block\Adminhtml\Businesshours\Edit\Form;

/**
 * Interceptor class for @see \Webkul\Helpdesk\Block\Adminhtml\Businesshours\Edit\Form
 */
class Interceptor extends \Webkul\Helpdesk\Block\Adminhtml\Businesshours\Edit\Form implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\Block\Template\Context $context, \Magento\Framework\Registry $registry, \Magento\Framework\Data\FormFactory $formFactory, array $data = [])
    {
        $this->___init();
        parent::__construct($context, $registry, $formFactory, $data);
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
