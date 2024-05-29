<?php
namespace Magento\Review\Block\Adminhtml\Main;

/**
 * Interceptor class for @see \Magento\Review\Block\Adminhtml\Main
 */
class Interceptor extends \Magento\Review\Block\Adminhtml\Main implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\Block\Widget\Context $context, \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository, \Magento\Catalog\Model\ProductFactory $productFactory, \Magento\Framework\Registry $registry, \Magento\Customer\Helper\View $customerViewHelper, array $data = [], ?\Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory = null)
    {
        $this->___init();
        parent::__construct($context, $customerRepository, $productFactory, $registry, $customerViewHelper, $data, $productCollectionFactory);
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
