<?php
namespace Smartwave\Dailydeals\Block\Adminhtml\Dailydeal\Edit;

/**
 * Interceptor class for @see \Smartwave\Dailydeals\Block\Adminhtml\Dailydeal\Edit
 */
class Interceptor extends \Smartwave\Dailydeals\Block\Adminhtml\Dailydeal\Edit implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\Registry $coreRegistry, \Magento\Backend\Block\Widget\Context $context, array $data = [])
    {
        $this->___init();
        parent::__construct($coreRegistry, $context, $data);
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
