<?php
namespace Meetanshi\OrderTracking\Block\Adminhtml\Carrier\Edit\Tab\General;

/**
 * Interceptor class for @see \Meetanshi\OrderTracking\Block\Adminhtml\Carrier\Edit\Tab\General
 */
class Interceptor extends \Meetanshi\OrderTracking\Block\Adminhtml\Carrier\Edit\Tab\General implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Store\Model\System\Store $systemStore, \Magento\Framework\Data\FormFactory $formFactory, \Magento\Framework\Registry $registry, \Magento\Backend\Block\Widget\Context $context, \Meetanshi\OrderTracking\Helper\Data $helper)
    {
        $this->___init();
        parent::__construct($systemStore, $formFactory, $registry, $context, $helper);
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
