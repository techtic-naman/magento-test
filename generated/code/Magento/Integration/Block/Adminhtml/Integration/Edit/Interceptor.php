<?php
namespace Magento\Integration\Block\Adminhtml\Integration\Edit;

/**
 * Interceptor class for @see \Magento\Integration\Block\Adminhtml\Integration\Edit
 */
class Interceptor extends \Magento\Integration\Block\Adminhtml\Integration\Edit implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\Block\Widget\Context $context, \Magento\Framework\Registry $registry, \Magento\Integration\Helper\Data $integrationHelper, array $data = [])
    {
        $this->___init();
        parent::__construct($context, $registry, $integrationHelper, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function addButton($buttonId, $data, $level = 0, $sortOrder = 0, $region = 'toolbar')
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'addButton');
        return $pluginInfo ? $this->___callPlugins('addButton', func_get_args(), $pluginInfo) : parent::addButton($buttonId, $data, $level, $sortOrder, $region);
    }
}
