<?php
namespace Webkul\Helpdesk\Block\Adminhtml\Agentsmanagement\Role\Grid;

/**
 * Interceptor class for @see \Webkul\Helpdesk\Block\Adminhtml\Agentsmanagement\Role\Grid
 */
class Interceptor extends \Webkul\Helpdesk\Block\Adminhtml\Agentsmanagement\Role\Grid implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\Block\Widget\Context $context, array $data = [])
    {
        $this->___init();
        parent::__construct($context, $data);
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