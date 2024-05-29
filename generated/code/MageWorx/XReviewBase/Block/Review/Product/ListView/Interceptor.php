<?php
namespace MageWorx\XReviewBase\Block\Review\Product\ListView;

/**
 * Interceptor class for @see \MageWorx\XReviewBase\Block\Review\Product\ListView
 */
class Interceptor extends \MageWorx\XReviewBase\Block\Review\Product\ListView implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\MageWorx\XReviewBase\Model\ConfigProvider $configProvider, \MageWorx\XReviewBase\Model\ResourceModel\Review $reviewResource, \Magento\Review\Model\ReviewSummaryFactory $reviewSummaryFactory, \Magento\Catalog\Block\Product\Context $context, \Magento\Framework\Url\EncoderInterface $urlEncoder, \Magento\Framework\Json\EncoderInterface $jsonEncoder, \Magento\Framework\Stdlib\StringUtils $string, \Magento\Catalog\Helper\Product $productHelper, \Magento\Catalog\Model\ProductTypes\ConfigInterface $productTypeConfig, \Magento\Framework\Locale\FormatInterface $localeFormat, \Magento\Customer\Model\Session $customerSession, \Magento\Catalog\Api\ProductRepositoryInterface $productRepository, \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency, \Magento\Review\Model\ResourceModel\Review\CollectionFactory $collectionFactory, \Magento\Directory\Model\ResourceModel\Country\CollectionFactory $countryCollectionFactory, \Magento\Framework\Data\Form\FormKey $formKey, \Magento\Framework\Module\Manager $moduleManager, array $data = [])
    {
        $this->___init();
        parent::__construct($configProvider, $reviewResource, $reviewSummaryFactory, $context, $urlEncoder, $jsonEncoder, $string, $productHelper, $productTypeConfig, $localeFormat, $customerSession, $productRepository, $priceCurrency, $collectionFactory, $countryCollectionFactory, $formKey, $moduleManager, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function canEmailToFriend()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'canEmailToFriend');
        return $pluginInfo ? $this->___callPlugins('canEmailToFriend', func_get_args(), $pluginInfo) : parent::canEmailToFriend();
    }

    /**
     * {@inheritdoc}
     */
    public function getQuantityValidators()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getQuantityValidators');
        return $pluginInfo ? $this->___callPlugins('getQuantityValidators', func_get_args(), $pluginInfo) : parent::getQuantityValidators();
    }

    /**
     * {@inheritdoc}
     */
    public function getImage($product, $imageId, $attributes = [])
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getImage');
        return $pluginInfo ? $this->___callPlugins('getImage', func_get_args(), $pluginInfo) : parent::getImage($product, $imageId, $attributes);
    }
}
