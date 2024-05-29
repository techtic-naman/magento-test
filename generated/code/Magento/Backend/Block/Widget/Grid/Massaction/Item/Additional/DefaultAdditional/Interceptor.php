<?php
namespace Magento\Backend\Block\Widget\Grid\Massaction\Item\Additional\DefaultAdditional;

/**
 * Interceptor class for @see \Magento\Backend\Block\Widget\Grid\Massaction\Item\Additional\DefaultAdditional
 */
class Interceptor extends \Magento\Backend\Block\Widget\Grid\Massaction\Item\Additional\DefaultAdditional implements \Magento\Framework\Interception\InterceptorInterface
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
