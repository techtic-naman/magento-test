<?php
namespace Magento\InventoryShippingAdminUi\Block\Adminhtml\Order\View\ShipButton;

/**
 * Interceptor class for @see \Magento\InventoryShippingAdminUi\Block\Adminhtml\Order\View\ShipButton
 */
class Interceptor extends \Magento\InventoryShippingAdminUi\Block\Adminhtml\Order\View\ShipButton implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\Block\Widget\Context $context, \Magento\Framework\Registry $registry, \Magento\InventoryShippingAdminUi\Model\IsWebsiteInMultiSourceMode $isWebsiteInMultiSourceMode, array $data = [], ?\Magento\InventoryShippingAdminUi\Model\IsOrderSourceManageable $isOrderSourceManageable = null)
    {
        $this->___init();
        parent::__construct($context, $registry, $isWebsiteInMultiSourceMode, $data, $isOrderSourceManageable);
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
