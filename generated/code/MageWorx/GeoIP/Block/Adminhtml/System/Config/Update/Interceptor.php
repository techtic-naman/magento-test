<?php
namespace MageWorx\GeoIP\Block\Adminhtml\System\Config\Update;

/**
 * Interceptor class for @see \MageWorx\GeoIP\Block\Adminhtml\System\Config\Update
 */
class Interceptor extends \MageWorx\GeoIP\Block\Adminhtml\System\Config\Update implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\Block\Template\Context $context, \MageWorx\GeoIP\Helper\Database $helperDatabase, array $data = [])
    {
        $this->___init();
        parent::__construct($context, $helperDatabase, $data);
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
