<?php
namespace Meetanshi\OrderTracking\Block\Adminhtml\Carrier\Edit\Form;

/**
 * Interceptor class for @see \Meetanshi\OrderTracking\Block\Adminhtml\Carrier\Edit\Form
 */
class Interceptor extends \Meetanshi\OrderTracking\Block\Adminhtml\Carrier\Edit\Form implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\Data\FormFactory $formFactory, \Magento\Backend\Block\Widget\Context $context)
    {
        $this->___init();
        parent::__construct($formFactory, $context);
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
