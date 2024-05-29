<?php
namespace Webkul\Walletsystem\Model\Quote\Item;

/**
 * Interceptor class for @see \Webkul\Walletsystem\Model\Quote\Item
 */
class Interceptor extends \Webkul\Walletsystem\Model\Quote\Item implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\Model\Context $context, \Magento\Framework\Registry $registry, \Magento\Framework\Api\ExtensionAttributesFactory $extensionFactory, \Magento\Framework\Api\AttributeValueFactory $customAttributeFactory, \Magento\Catalog\Api\ProductRepositoryInterface $productRepository, \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency, \Magento\Sales\Model\Status\ListFactory $statusListFactory, \Magento\Framework\Locale\FormatInterface $localeFormat, \Magento\Quote\Model\Quote\Item\OptionFactory $itemOptionFactory, \Magento\Quote\Model\Quote\Item\Compare $quoteItemCompare, \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry, \Webkul\Walletsystem\Helper\Data $helper, \Magento\Framework\App\Request\Http $request, ?\Magento\Framework\Model\ResourceModel\AbstractResource $resource = null, ?\Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null, ?\Magento\Framework\Serialize\Serializer\Json $serializer = null, array $data = [])
    {
        $this->___init();
        parent::__construct($context, $registry, $extensionFactory, $customAttributeFactory, $productRepository, $priceCurrency, $statusListFactory, $localeFormat, $itemOptionFactory, $quoteItemCompare, $stockRegistry, $helper, $request, $resource, $resourceCollection, $serializer, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function checkData()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'checkData');
        return $pluginInfo ? $this->___callPlugins('checkData', func_get_args(), $pluginInfo) : parent::checkData();
    }
}
