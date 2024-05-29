<?php
namespace Magento\InventoryInStorePickupSalesAdminUi\Block\Adminhtml\Order\View\ReadyForPickup;

/**
 * Interceptor class for @see \Magento\InventoryInStorePickupSalesAdminUi\Block\Adminhtml\Order\View\ReadyForPickup
 */
class Interceptor extends \Magento\InventoryInStorePickupSalesAdminUi\Block\Adminhtml\Order\View\ReadyForPickup implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\Block\Widget\Context $context, \Magento\Sales\Block\Adminhtml\Order\View $viewBlock, \Magento\InventoryInStorePickupSalesAdminUi\Model\IsRenderReadyForPickupButton $isDisplayButton, \Magento\Sales\Api\ShipmentRepositoryInterface $shipmentRepository, \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder, array $data = [])
    {
        $this->___init();
        parent::__construct($context, $viewBlock, $isDisplayButton, $shipmentRepository, $searchCriteriaBuilder, $data);
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
