<?php
namespace Webkul\Walletsystem\Block\Adminhtml\Wallet\Edit\Tab\Form;

/**
 * Interceptor class for @see \Webkul\Walletsystem\Block\Adminhtml\Wallet\Edit\Tab\Form
 */
class Interceptor extends \Webkul\Walletsystem\Block\Adminhtml\Wallet\Edit\Tab\Form implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\Block\Template\Context $context, \Magento\Framework\Registry $registry, \Magento\Framework\Data\FormFactory $formFactory, \Webkul\Walletsystem\Helper\Data $helper, array $data = [])
    {
        $this->___init();
        parent::__construct($context, $registry, $formFactory, $helper, $data);
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