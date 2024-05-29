<?php
namespace Smartwave\Porto\Block\System\Config\Form\Button\Import\Cms;

/**
 * Interceptor class for @see \Smartwave\Porto\Block\System\Config\Form\Button\Import\Cms
 */
class Interceptor extends \Smartwave\Porto\Block\System\Config\Form\Button\Import\Cms implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\Block\Template\Context $context, \Smartwave\Porto\Helper\Data $helper)
    {
        $this->___init();
        parent::__construct($context, $helper);
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
