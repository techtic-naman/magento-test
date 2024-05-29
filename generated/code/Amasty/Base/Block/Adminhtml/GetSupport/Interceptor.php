<?php
namespace Amasty\Base\Block\Adminhtml\GetSupport;

/**
 * Interceptor class for @see \Amasty\Base\Block\Adminhtml\GetSupport
 */
class Interceptor extends \Amasty\Base\Block\Adminhtml\GetSupport implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\Block\Template\Context $context, \Amasty\Base\ViewModel\GetSupport $getSupportViewModel, array $data = [])
    {
        $this->___init();
        parent::__construct($context, $getSupportViewModel, $data);
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
