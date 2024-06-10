<?php
namespace Webkul\Marketplace\Helper\Data;

/**
 * Interceptor class for @see \Webkul\Marketplace\Helper\Data
 */
class Interceptor extends \Webkul\Marketplace\Helper\Data implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\App\Helper\Context $context, \Magento\Framework\ObjectManagerInterface $objectManager, \Magento\Customer\Model\SessionFactory $customerSessionFactory, \Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\CollectionFactory $collectionFactory, \Magento\Framework\App\Http\Context $httpContext, \Magento\Catalog\Model\ResourceModel\Product $product, \Magento\Store\Model\StoreManagerInterface $storeManager, \Magento\Directory\Model\Currency $currency, \Magento\Framework\Locale\CurrencyInterface $localeCurrency, \Magento\Framework\App\Cache\ManagerFactory $cacheManagerFactory, \Magento\Framework\View\Element\BlockFactory $blockFactory, \Webkul\Marketplace\Model\ResourceModel\Seller\CollectionFactory $sellerCollectionFactory, \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory, \Webkul\Marketplace\Model\ResourceModel\Product\CollectionFactory $mpProductCollectionFactory, \Webkul\Marketplace\Model\ResourceModel\Feedback\CollectionFactory $mpFeedbackCollectionFactory, \Magento\Framework\App\ResourceConnection $resource, \Magento\Framework\Locale\FormatInterface $localeFormat, \Magento\Framework\Filesystem\Driver\File $file, \Magento\Indexer\Model\IndexerFactory $indexerFactory, \Webkul\Marketplace\Model\Seller $marketplaceSeller, \Magento\Indexer\Model\Indexer\CollectionFactory $indexerCollectionFactory, \Magento\Customer\Model\CustomerFactory $customerModel, \Magento\Catalog\Model\Product\Visibility $visibility, \Webkul\Marketplace\Model\ControllersRepository $controllersRepository, \Magento\UrlRewrite\Model\UrlRewriteFactory $urlRewriteFactory, \Webkul\Marketplace\Logger\Logger $logger, \Webkul\Marketplace\Model\SaleperpartnerFactory $mpSalesPartner, \Webkul\Marketplace\Model\ResourceModel\VendorAttributeMapping\CollectionFactory $mappingColl, \Magento\Framework\Module\Manager $moduleManager, \Magento\Framework\Serialize\SerializerInterface $serializer, \Webkul\Marketplace\Model\ResourceModel\Saleslist\CollectionFactory $salesListCollection)
    {
        $this->___init();
        parent::__construct($context, $objectManager, $customerSessionFactory, $collectionFactory, $httpContext, $product, $storeManager, $currency, $localeCurrency, $cacheManagerFactory, $blockFactory, $sellerCollectionFactory, $productCollectionFactory, $mpProductCollectionFactory, $mpFeedbackCollectionFactory, $resource, $localeFormat, $file, $indexerFactory, $marketplaceSeller, $indexerCollectionFactory, $customerModel, $visibility, $controllersRepository, $urlRewriteFactory, $logger, $mpSalesPartner, $mappingColl, $moduleManager, $serializer, $salesListCollection);
    }

    /**
     * {@inheritdoc}
     */
    public function getCustomer()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getCustomer');
        return $pluginInfo ? $this->___callPlugins('getCustomer', func_get_args(), $pluginInfo) : parent::getCustomer();
    }

    /**
     * {@inheritdoc}
     */
    public function getCustomerData($id = null)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getCustomerData');
        return $pluginInfo ? $this->___callPlugins('getCustomerData', func_get_args(), $pluginInfo) : parent::getCustomerData($id);
    }

    /**
     * {@inheritdoc}
     */
    public function getCustomerId()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getCustomerId');
        return $pluginInfo ? $this->___callPlugins('getCustomerId', func_get_args(), $pluginInfo) : parent::getCustomerId();
    }

    /**
     * {@inheritdoc}
     */
    public function isCustomerLoggedIn()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'isCustomerLoggedIn');
        return $pluginInfo ? $this->___callPlugins('isCustomerLoggedIn', func_get_args(), $pluginInfo) : parent::isCustomerLoggedIn();
    }

    /**
     * {@inheritdoc}
     */
    public function isSeller()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'isSeller');
        return $pluginInfo ? $this->___callPlugins('isSeller', func_get_args(), $pluginInfo) : parent::isSeller();
    }

    /**
     * {@inheritdoc}
     */
    public function isRightSeller($productId = '')
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'isRightSeller');
        return $pluginInfo ? $this->___callPlugins('isRightSeller', func_get_args(), $pluginInfo) : parent::isRightSeller($productId);
    }

    /**
     * {@inheritdoc}
     */
    public function getSellerData()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getSellerData');
        return $pluginInfo ? $this->___callPlugins('getSellerData', func_get_args(), $pluginInfo) : parent::getSellerData();
    }

    /**
     * {@inheritdoc}
     */
    public function getSellerProductData()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getSellerProductData');
        return $pluginInfo ? $this->___callPlugins('getSellerProductData', func_get_args(), $pluginInfo) : parent::getSellerProductData();
    }

    /**
     * {@inheritdoc}
     */
    public function getSellerProductDataByProductId($productId = '')
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getSellerProductDataByProductId');
        return $pluginInfo ? $this->___callPlugins('getSellerProductDataByProductId', func_get_args(), $pluginInfo) : parent::getSellerProductDataByProductId($productId);
    }

    /**
     * {@inheritdoc}
     */
    public function getSellerDataBySellerId($sellerId = '')
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getSellerDataBySellerId');
        return $pluginInfo ? $this->___callPlugins('getSellerDataBySellerId', func_get_args(), $pluginInfo) : parent::getSellerDataBySellerId($sellerId);
    }

    /**
     * {@inheritdoc}
     */
    public function getSellerDataByShopUrl($shopUrl = '')
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getSellerDataByShopUrl');
        return $pluginInfo ? $this->___callPlugins('getSellerDataByShopUrl', func_get_args(), $pluginInfo) : parent::getSellerDataByShopUrl($shopUrl);
    }

    /**
     * {@inheritdoc}
     */
    public function getRootCategoryIdByStoreId($storeId = '')
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getRootCategoryIdByStoreId');
        return $pluginInfo ? $this->___callPlugins('getRootCategoryIdByStoreId', func_get_args(), $pluginInfo) : parent::getRootCategoryIdByStoreId($storeId);
    }

    /**
     * {@inheritdoc}
     */
    public function getAllStores()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getAllStores');
        return $pluginInfo ? $this->___callPlugins('getAllStores', func_get_args(), $pluginInfo) : parent::getAllStores();
    }

    /**
     * {@inheritdoc}
     */
    public function getCurrentStoreId()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getCurrentStoreId');
        return $pluginInfo ? $this->___callPlugins('getCurrentStoreId', func_get_args(), $pluginInfo) : parent::getCurrentStoreId();
    }

    /**
     * {@inheritdoc}
     */
    public function getWebsiteId()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getWebsiteId');
        return $pluginInfo ? $this->___callPlugins('getWebsiteId', func_get_args(), $pluginInfo) : parent::getWebsiteId();
    }

    /**
     * {@inheritdoc}
     */
    public function getAllWebsites()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getAllWebsites');
        return $pluginInfo ? $this->___callPlugins('getAllWebsites', func_get_args(), $pluginInfo) : parent::getAllWebsites();
    }

    /**
     * {@inheritdoc}
     */
    public function getSingleStoreStatus()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getSingleStoreStatus');
        return $pluginInfo ? $this->___callPlugins('getSingleStoreStatus', func_get_args(), $pluginInfo) : parent::getSingleStoreStatus();
    }

    /**
     * {@inheritdoc}
     */
    public function getSingleStoreModeStatus()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getSingleStoreModeStatus');
        return $pluginInfo ? $this->___callPlugins('getSingleStoreModeStatus', func_get_args(), $pluginInfo) : parent::getSingleStoreModeStatus();
    }

    /**
     * {@inheritdoc}
     */
    public function setCurrentStore($storeId)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'setCurrentStore');
        return $pluginInfo ? $this->___callPlugins('setCurrentStore', func_get_args(), $pluginInfo) : parent::setCurrentStore($storeId);
    }

    /**
     * {@inheritdoc}
     */
    public function getCurrentCurrencyRate()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getCurrentCurrencyRate');
        return $pluginInfo ? $this->___callPlugins('getCurrentCurrencyRate', func_get_args(), $pluginInfo) : parent::getCurrentCurrencyRate();
    }

    /**
     * {@inheritdoc}
     */
    public function getCurrentCurrencyCode()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getCurrentCurrencyCode');
        return $pluginInfo ? $this->___callPlugins('getCurrentCurrencyCode', func_get_args(), $pluginInfo) : parent::getCurrentCurrencyCode();
    }

    /**
     * {@inheritdoc}
     */
    public function getBaseCurrencyCode()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getBaseCurrencyCode');
        return $pluginInfo ? $this->___callPlugins('getBaseCurrencyCode', func_get_args(), $pluginInfo) : parent::getBaseCurrencyCode();
    }

    /**
     * {@inheritdoc}
     */
    public function getConfigAllowCurrencies()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getConfigAllowCurrencies');
        return $pluginInfo ? $this->___callPlugins('getConfigAllowCurrencies', func_get_args(), $pluginInfo) : parent::getConfigAllowCurrencies();
    }

    /**
     * {@inheritdoc}
     */
    public function getCurrencyRates($currency, $toCurrencies = null)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getCurrencyRates');
        return $pluginInfo ? $this->___callPlugins('getCurrencyRates', func_get_args(), $pluginInfo) : parent::getCurrencyRates($currency, $toCurrencies);
    }

    /**
     * {@inheritdoc}
     */
    public function getCurrencySymbol()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getCurrencySymbol');
        return $pluginInfo ? $this->___callPlugins('getCurrencySymbol', func_get_args(), $pluginInfo) : parent::getCurrencySymbol();
    }

    /**
     * {@inheritdoc}
     */
    public function getPriceFormat()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getPriceFormat');
        return $pluginInfo ? $this->___callPlugins('getPriceFormat', func_get_args(), $pluginInfo) : parent::getPriceFormat();
    }

    /**
     * {@inheritdoc}
     */
    public function getAllowedSets()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getAllowedSets');
        return $pluginInfo ? $this->___callPlugins('getAllowedSets', func_get_args(), $pluginInfo) : parent::getAllowedSets();
    }

    /**
     * {@inheritdoc}
     */
    public function getAllowedProductTypes()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getAllowedProductTypes');
        return $pluginInfo ? $this->___callPlugins('getAllowedProductTypes', func_get_args(), $pluginInfo) : parent::getAllowedProductTypes();
    }

    /**
     * {@inheritdoc}
     */
    public function getTaxClassModel()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getTaxClassModel');
        return $pluginInfo ? $this->___callPlugins('getTaxClassModel', func_get_args(), $pluginInfo) : parent::getTaxClassModel();
    }

    /**
     * {@inheritdoc}
     */
    public function getVisibilityOptionArray()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getVisibilityOptionArray');
        return $pluginInfo ? $this->___callPlugins('getVisibilityOptionArray', func_get_args(), $pluginInfo) : parent::getVisibilityOptionArray();
    }

    /**
     * {@inheritdoc}
     */
    public function isSellerExist()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'isSellerExist');
        return $pluginInfo ? $this->___callPlugins('isSellerExist', func_get_args(), $pluginInfo) : parent::isSellerExist();
    }

    /**
     * {@inheritdoc}
     */
    public function getSeller()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getSeller');
        return $pluginInfo ? $this->___callPlugins('getSeller', func_get_args(), $pluginInfo) : parent::getSeller();
    }

    /**
     * {@inheritdoc}
     */
    public function getSellerCollectionObj($sellerId, $storeId = null)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getSellerCollectionObj');
        return $pluginInfo ? $this->___callPlugins('getSellerCollectionObj', func_get_args(), $pluginInfo) : parent::getSellerCollectionObj($sellerId, $storeId);
    }

    /**
     * {@inheritdoc}
     */
    public function getSellerStatus($sellerId = 0)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getSellerStatus');
        return $pluginInfo ? $this->___callPlugins('getSellerStatus', func_get_args(), $pluginInfo) : parent::getSellerStatus($sellerId);
    }

    /**
     * {@inheritdoc}
     */
    public function getAllowedSellerStatus()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getAllowedSellerStatus');
        return $pluginInfo ? $this->___callPlugins('getAllowedSellerStatus', func_get_args(), $pluginInfo) : parent::getAllowedSellerStatus();
    }

    /**
     * {@inheritdoc}
     */
    public function getSellerCollectionObjByShop($shopUrl, $storeId = null)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getSellerCollectionObjByShop');
        return $pluginInfo ? $this->___callPlugins('getSellerCollectionObjByShop', func_get_args(), $pluginInfo) : parent::getSellerCollectionObjByShop($shopUrl, $storeId);
    }

    /**
     * {@inheritdoc}
     */
    public function getFeedTotal($sellerId)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getFeedTotal');
        return $pluginInfo ? $this->___callPlugins('getFeedTotal', func_get_args(), $pluginInfo) : parent::getFeedTotal($sellerId);
    }

    /**
     * {@inheritdoc}
     */
    public function getSelleRating($sellerId)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getSelleRating');
        return $pluginInfo ? $this->___callPlugins('getSelleRating', func_get_args(), $pluginInfo) : parent::getSelleRating($sellerId);
    }

    /**
     * {@inheritdoc}
     */
    public function getCatatlogGridPerPageValues()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getCatatlogGridPerPageValues');
        return $pluginInfo ? $this->___callPlugins('getCatatlogGridPerPageValues', func_get_args(), $pluginInfo) : parent::getCatatlogGridPerPageValues();
    }

    /**
     * {@inheritdoc}
     */
    public function getConfigValue($group = '', $field = '')
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getConfigValue');
        return $pluginInfo ? $this->___callPlugins('getConfigValue', func_get_args(), $pluginInfo) : parent::getConfigValue($group, $field);
    }

    /**
     * {@inheritdoc}
     */
    public function getCaptchaEnable()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getCaptchaEnable');
        return $pluginInfo ? $this->___callPlugins('getCaptchaEnable', func_get_args(), $pluginInfo) : parent::getCaptchaEnable();
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultTransEmailId()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getDefaultTransEmailId');
        return $pluginInfo ? $this->___callPlugins('getDefaultTransEmailId', func_get_args(), $pluginInfo) : parent::getDefaultTransEmailId();
    }

    /**
     * {@inheritdoc}
     */
    public function getAdminEmailId()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getAdminEmailId');
        return $pluginInfo ? $this->___callPlugins('getAdminEmailId', func_get_args(), $pluginInfo) : parent::getAdminEmailId();
    }

    /**
     * {@inheritdoc}
     */
    public function getAllowedCategoryIds()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getAllowedCategoryIds');
        return $pluginInfo ? $this->___callPlugins('getAllowedCategoryIds', func_get_args(), $pluginInfo) : parent::getAllowedCategoryIds();
    }

    /**
     * {@inheritdoc}
     */
    public function getIsProductEditApproval()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getIsProductEditApproval');
        return $pluginInfo ? $this->___callPlugins('getIsProductEditApproval', func_get_args(), $pluginInfo) : parent::getIsProductEditApproval();
    }

    /**
     * {@inheritdoc}
     */
    public function getIsPartnerApproval()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getIsPartnerApproval');
        return $pluginInfo ? $this->___callPlugins('getIsPartnerApproval', func_get_args(), $pluginInfo) : parent::getIsPartnerApproval();
    }

    /**
     * {@inheritdoc}
     */
    public function getIsProductApproval()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getIsProductApproval');
        return $pluginInfo ? $this->___callPlugins('getIsProductApproval', func_get_args(), $pluginInfo) : parent::getIsProductApproval();
    }

    /**
     * {@inheritdoc}
     */
    public function getAllowedAttributesetIds()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getAllowedAttributesetIds');
        return $pluginInfo ? $this->___callPlugins('getAllowedAttributesetIds', func_get_args(), $pluginInfo) : parent::getAllowedAttributesetIds();
    }

    /**
     * {@inheritdoc}
     */
    public function getAllowedProductType()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getAllowedProductType');
        return $pluginInfo ? $this->___callPlugins('getAllowedProductType', func_get_args(), $pluginInfo) : parent::getAllowedProductType();
    }

    /**
     * {@inheritdoc}
     */
    public function getUseCommissionRule()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getUseCommissionRule');
        return $pluginInfo ? $this->___callPlugins('getUseCommissionRule', func_get_args(), $pluginInfo) : parent::getUseCommissionRule();
    }

    /**
     * {@inheritdoc}
     */
    public function getCommissionType()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getCommissionType');
        return $pluginInfo ? $this->___callPlugins('getCommissionType', func_get_args(), $pluginInfo) : parent::getCommissionType();
    }

    /**
     * {@inheritdoc}
     */
    public function getIsOrderManage()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getIsOrderManage');
        return $pluginInfo ? $this->___callPlugins('getIsOrderManage', func_get_args(), $pluginInfo) : parent::getIsOrderManage();
    }

    /**
     * {@inheritdoc}
     */
    public function getConfigCommissionRate()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getConfigCommissionRate');
        return $pluginInfo ? $this->___callPlugins('getConfigCommissionRate', func_get_args(), $pluginInfo) : parent::getConfigCommissionRate();
    }

    /**
     * {@inheritdoc}
     */
    public function getConfigCommissionWithDiscount()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getConfigCommissionWithDiscount');
        return $pluginInfo ? $this->___callPlugins('getConfigCommissionWithDiscount', func_get_args(), $pluginInfo) : parent::getConfigCommissionWithDiscount();
    }

    /**
     * {@inheritdoc}
     */
    public function getConfigTaxManage()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getConfigTaxManage');
        return $pluginInfo ? $this->___callPlugins('getConfigTaxManage', func_get_args(), $pluginInfo) : parent::getConfigTaxManage();
    }

    /**
     * {@inheritdoc}
     */
    public function getlowStockNotification()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getlowStockNotification');
        return $pluginInfo ? $this->___callPlugins('getlowStockNotification', func_get_args(), $pluginInfo) : parent::getlowStockNotification();
    }

    /**
     * {@inheritdoc}
     */
    public function getlowStockQty()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getlowStockQty');
        return $pluginInfo ? $this->___callPlugins('getlowStockQty', func_get_args(), $pluginInfo) : parent::getlowStockQty();
    }

    /**
     * {@inheritdoc}
     */
    public function getActiveColorPicker()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getActiveColorPicker');
        return $pluginInfo ? $this->___callPlugins('getActiveColorPicker', func_get_args(), $pluginInfo) : parent::getActiveColorPicker();
    }

    /**
     * {@inheritdoc}
     */
    public function getSellerPolicyApproval()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getSellerPolicyApproval');
        return $pluginInfo ? $this->___callPlugins('getSellerPolicyApproval', func_get_args(), $pluginInfo) : parent::getSellerPolicyApproval();
    }

    /**
     * {@inheritdoc}
     */
    public function getUrlRewrite()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getUrlRewrite');
        return $pluginInfo ? $this->___callPlugins('getUrlRewrite', func_get_args(), $pluginInfo) : parent::getUrlRewrite();
    }

    /**
     * {@inheritdoc}
     */
    public function getReviewStatus()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getReviewStatus');
        return $pluginInfo ? $this->___callPlugins('getReviewStatus', func_get_args(), $pluginInfo) : parent::getReviewStatus();
    }

    /**
     * {@inheritdoc}
     */
    public function getMarketplaceHeadLabel()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getMarketplaceHeadLabel');
        return $pluginInfo ? $this->___callPlugins('getMarketplaceHeadLabel', func_get_args(), $pluginInfo) : parent::getMarketplaceHeadLabel();
    }

    /**
     * {@inheritdoc}
     */
    public function getMarketplacelabel1()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getMarketplacelabel1');
        return $pluginInfo ? $this->___callPlugins('getMarketplacelabel1', func_get_args(), $pluginInfo) : parent::getMarketplacelabel1();
    }

    /**
     * {@inheritdoc}
     */
    public function getMarketplacelabel2()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getMarketplacelabel2');
        return $pluginInfo ? $this->___callPlugins('getMarketplacelabel2', func_get_args(), $pluginInfo) : parent::getMarketplacelabel2();
    }

    /**
     * {@inheritdoc}
     */
    public function getMarketplacelabel3()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getMarketplacelabel3');
        return $pluginInfo ? $this->___callPlugins('getMarketplacelabel3', func_get_args(), $pluginInfo) : parent::getMarketplacelabel3();
    }

    /**
     * {@inheritdoc}
     */
    public function getMarketplacelabel4()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getMarketplacelabel4');
        return $pluginInfo ? $this->___callPlugins('getMarketplacelabel4', func_get_args(), $pluginInfo) : parent::getMarketplacelabel4();
    }

    /**
     * {@inheritdoc}
     */
    public function getDisplayBanner()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getDisplayBanner');
        return $pluginInfo ? $this->___callPlugins('getDisplayBanner', func_get_args(), $pluginInfo) : parent::getDisplayBanner();
    }

    /**
     * {@inheritdoc}
     */
    public function getBannerImage()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getBannerImage');
        return $pluginInfo ? $this->___callPlugins('getBannerImage', func_get_args(), $pluginInfo) : parent::getBannerImage();
    }

    /**
     * {@inheritdoc}
     */
    public function getBannerContent()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getBannerContent');
        return $pluginInfo ? $this->___callPlugins('getBannerContent', func_get_args(), $pluginInfo) : parent::getBannerContent();
    }

    /**
     * {@inheritdoc}
     */
    public function getDisplayIcon()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getDisplayIcon');
        return $pluginInfo ? $this->___callPlugins('getDisplayIcon', func_get_args(), $pluginInfo) : parent::getDisplayIcon();
    }

    /**
     * {@inheritdoc}
     */
    public function getIconImage1()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getIconImage1');
        return $pluginInfo ? $this->___callPlugins('getIconImage1', func_get_args(), $pluginInfo) : parent::getIconImage1();
    }

    /**
     * {@inheritdoc}
     */
    public function getIconImageLabel1()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getIconImageLabel1');
        return $pluginInfo ? $this->___callPlugins('getIconImageLabel1', func_get_args(), $pluginInfo) : parent::getIconImageLabel1();
    }

    /**
     * {@inheritdoc}
     */
    public function getIconImage2()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getIconImage2');
        return $pluginInfo ? $this->___callPlugins('getIconImage2', func_get_args(), $pluginInfo) : parent::getIconImage2();
    }

    /**
     * {@inheritdoc}
     */
    public function getIconImageLabel2()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getIconImageLabel2');
        return $pluginInfo ? $this->___callPlugins('getIconImageLabel2', func_get_args(), $pluginInfo) : parent::getIconImageLabel2();
    }

    /**
     * {@inheritdoc}
     */
    public function getIconImage3()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getIconImage3');
        return $pluginInfo ? $this->___callPlugins('getIconImage3', func_get_args(), $pluginInfo) : parent::getIconImage3();
    }

    /**
     * {@inheritdoc}
     */
    public function getIconImageLabel3()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getIconImageLabel3');
        return $pluginInfo ? $this->___callPlugins('getIconImageLabel3', func_get_args(), $pluginInfo) : parent::getIconImageLabel3();
    }

    /**
     * {@inheritdoc}
     */
    public function getIconImage4()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getIconImage4');
        return $pluginInfo ? $this->___callPlugins('getIconImage4', func_get_args(), $pluginInfo) : parent::getIconImage4();
    }

    /**
     * {@inheritdoc}
     */
    public function getIconImageLabel4()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getIconImageLabel4');
        return $pluginInfo ? $this->___callPlugins('getIconImageLabel4', func_get_args(), $pluginInfo) : parent::getIconImageLabel4();
    }

    /**
     * {@inheritdoc}
     */
    public function getMarketplacebutton()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getMarketplacebutton');
        return $pluginInfo ? $this->___callPlugins('getMarketplacebutton', func_get_args(), $pluginInfo) : parent::getMarketplacebutton();
    }

    /**
     * {@inheritdoc}
     */
    public function getMarketplaceprofile()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getMarketplaceprofile');
        return $pluginInfo ? $this->___callPlugins('getMarketplaceprofile', func_get_args(), $pluginInfo) : parent::getMarketplaceprofile();
    }

    /**
     * {@inheritdoc}
     */
    public function getSellerlisttopLabel()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getSellerlisttopLabel');
        return $pluginInfo ? $this->___callPlugins('getSellerlisttopLabel', func_get_args(), $pluginInfo) : parent::getSellerlisttopLabel();
    }

    /**
     * {@inheritdoc}
     */
    public function getSellerlistbottomLabel()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getSellerlistbottomLabel');
        return $pluginInfo ? $this->___callPlugins('getSellerlistbottomLabel', func_get_args(), $pluginInfo) : parent::getSellerlistbottomLabel();
    }

    /**
     * {@inheritdoc}
     */
    public function getProductHintStatus()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getProductHintStatus');
        return $pluginInfo ? $this->___callPlugins('getProductHintStatus', func_get_args(), $pluginInfo) : parent::getProductHintStatus();
    }

    /**
     * {@inheritdoc}
     */
    public function getProductHintCategory()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getProductHintCategory');
        return $pluginInfo ? $this->___callPlugins('getProductHintCategory', func_get_args(), $pluginInfo) : parent::getProductHintCategory();
    }

    /**
     * {@inheritdoc}
     */
    public function getProductHintName()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getProductHintName');
        return $pluginInfo ? $this->___callPlugins('getProductHintName', func_get_args(), $pluginInfo) : parent::getProductHintName();
    }

    /**
     * {@inheritdoc}
     */
    public function getProductHintDesc()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getProductHintDesc');
        return $pluginInfo ? $this->___callPlugins('getProductHintDesc', func_get_args(), $pluginInfo) : parent::getProductHintDesc();
    }

    /**
     * {@inheritdoc}
     */
    public function getProductHintShortDesc()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getProductHintShortDesc');
        return $pluginInfo ? $this->___callPlugins('getProductHintShortDesc', func_get_args(), $pluginInfo) : parent::getProductHintShortDesc();
    }

    /**
     * {@inheritdoc}
     */
    public function getProductHintSku()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getProductHintSku');
        return $pluginInfo ? $this->___callPlugins('getProductHintSku', func_get_args(), $pluginInfo) : parent::getProductHintSku();
    }

    /**
     * {@inheritdoc}
     */
    public function getProductHintPrice()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getProductHintPrice');
        return $pluginInfo ? $this->___callPlugins('getProductHintPrice', func_get_args(), $pluginInfo) : parent::getProductHintPrice();
    }

    /**
     * {@inheritdoc}
     */
    public function getProductHintSpecialPrice()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getProductHintSpecialPrice');
        return $pluginInfo ? $this->___callPlugins('getProductHintSpecialPrice', func_get_args(), $pluginInfo) : parent::getProductHintSpecialPrice();
    }

    /**
     * {@inheritdoc}
     */
    public function getProductHintStartDate()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getProductHintStartDate');
        return $pluginInfo ? $this->___callPlugins('getProductHintStartDate', func_get_args(), $pluginInfo) : parent::getProductHintStartDate();
    }

    /**
     * {@inheritdoc}
     */
    public function getProductHintEndDate()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getProductHintEndDate');
        return $pluginInfo ? $this->___callPlugins('getProductHintEndDate', func_get_args(), $pluginInfo) : parent::getProductHintEndDate();
    }

    /**
     * {@inheritdoc}
     */
    public function getProductHintQty()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getProductHintQty');
        return $pluginInfo ? $this->___callPlugins('getProductHintQty', func_get_args(), $pluginInfo) : parent::getProductHintQty();
    }

    /**
     * {@inheritdoc}
     */
    public function getProductHintStock()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getProductHintStock');
        return $pluginInfo ? $this->___callPlugins('getProductHintStock', func_get_args(), $pluginInfo) : parent::getProductHintStock();
    }

    /**
     * {@inheritdoc}
     */
    public function getProductHintTax()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getProductHintTax');
        return $pluginInfo ? $this->___callPlugins('getProductHintTax', func_get_args(), $pluginInfo) : parent::getProductHintTax();
    }

    /**
     * {@inheritdoc}
     */
    public function getProductHintWeight()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getProductHintWeight');
        return $pluginInfo ? $this->___callPlugins('getProductHintWeight', func_get_args(), $pluginInfo) : parent::getProductHintWeight();
    }

    /**
     * {@inheritdoc}
     */
    public function getProductHintImage()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getProductHintImage');
        return $pluginInfo ? $this->___callPlugins('getProductHintImage', func_get_args(), $pluginInfo) : parent::getProductHintImage();
    }

    /**
     * {@inheritdoc}
     */
    public function getProductHintEnable()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getProductHintEnable');
        return $pluginInfo ? $this->___callPlugins('getProductHintEnable', func_get_args(), $pluginInfo) : parent::getProductHintEnable();
    }

    /**
     * {@inheritdoc}
     */
    public function getProfileHintStatus()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getProfileHintStatus');
        return $pluginInfo ? $this->___callPlugins('getProfileHintStatus', func_get_args(), $pluginInfo) : parent::getProfileHintStatus();
    }

    /**
     * {@inheritdoc}
     */
    public function getProfileHintBecomeSeller()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getProfileHintBecomeSeller');
        return $pluginInfo ? $this->___callPlugins('getProfileHintBecomeSeller', func_get_args(), $pluginInfo) : parent::getProfileHintBecomeSeller();
    }

    /**
     * {@inheritdoc}
     */
    public function getProfileHintShopurl()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getProfileHintShopurl');
        return $pluginInfo ? $this->___callPlugins('getProfileHintShopurl', func_get_args(), $pluginInfo) : parent::getProfileHintShopurl();
    }

    /**
     * {@inheritdoc}
     */
    public function getProfileHintTw()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getProfileHintTw');
        return $pluginInfo ? $this->___callPlugins('getProfileHintTw', func_get_args(), $pluginInfo) : parent::getProfileHintTw();
    }

    /**
     * {@inheritdoc}
     */
    public function getProfileHintFb()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getProfileHintFb');
        return $pluginInfo ? $this->___callPlugins('getProfileHintFb', func_get_args(), $pluginInfo) : parent::getProfileHintFb();
    }

    /**
     * {@inheritdoc}
     */
    public function getProfileHintInsta()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getProfileHintInsta');
        return $pluginInfo ? $this->___callPlugins('getProfileHintInsta', func_get_args(), $pluginInfo) : parent::getProfileHintInsta();
    }

    /**
     * {@inheritdoc}
     */
    public function getProfileHintGoogle()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getProfileHintGoogle');
        return $pluginInfo ? $this->___callPlugins('getProfileHintGoogle', func_get_args(), $pluginInfo) : parent::getProfileHintGoogle();
    }

    /**
     * {@inheritdoc}
     */
    public function getProfileHintYoutube()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getProfileHintYoutube');
        return $pluginInfo ? $this->___callPlugins('getProfileHintYoutube', func_get_args(), $pluginInfo) : parent::getProfileHintYoutube();
    }

    /**
     * {@inheritdoc}
     */
    public function getProfileHintVimeo()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getProfileHintVimeo');
        return $pluginInfo ? $this->___callPlugins('getProfileHintVimeo', func_get_args(), $pluginInfo) : parent::getProfileHintVimeo();
    }

    /**
     * {@inheritdoc}
     */
    public function getProfileHintPinterest()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getProfileHintPinterest');
        return $pluginInfo ? $this->___callPlugins('getProfileHintPinterest', func_get_args(), $pluginInfo) : parent::getProfileHintPinterest();
    }

    /**
     * {@inheritdoc}
     */
    public function getProfileHintCn()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getProfileHintCn');
        return $pluginInfo ? $this->___callPlugins('getProfileHintCn', func_get_args(), $pluginInfo) : parent::getProfileHintCn();
    }

    /**
     * {@inheritdoc}
     */
    public function getProfileHintTax()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getProfileHintTax');
        return $pluginInfo ? $this->___callPlugins('getProfileHintTax', func_get_args(), $pluginInfo) : parent::getProfileHintTax();
    }

    /**
     * {@inheritdoc}
     */
    public function getProfileHintBc()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getProfileHintBc');
        return $pluginInfo ? $this->___callPlugins('getProfileHintBc', func_get_args(), $pluginInfo) : parent::getProfileHintBc();
    }

    /**
     * {@inheritdoc}
     */
    public function getProfileHintShop()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getProfileHintShop');
        return $pluginInfo ? $this->___callPlugins('getProfileHintShop', func_get_args(), $pluginInfo) : parent::getProfileHintShop();
    }

    /**
     * {@inheritdoc}
     */
    public function getProfileHintBanner()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getProfileHintBanner');
        return $pluginInfo ? $this->___callPlugins('getProfileHintBanner', func_get_args(), $pluginInfo) : parent::getProfileHintBanner();
    }

    /**
     * {@inheritdoc}
     */
    public function getProfileHintLogo()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getProfileHintLogo');
        return $pluginInfo ? $this->___callPlugins('getProfileHintLogo', func_get_args(), $pluginInfo) : parent::getProfileHintLogo();
    }

    /**
     * {@inheritdoc}
     */
    public function getProfileHintLoc()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getProfileHintLoc');
        return $pluginInfo ? $this->___callPlugins('getProfileHintLoc', func_get_args(), $pluginInfo) : parent::getProfileHintLoc();
    }

    /**
     * {@inheritdoc}
     */
    public function getProfileHintDesc()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getProfileHintDesc');
        return $pluginInfo ? $this->___callPlugins('getProfileHintDesc', func_get_args(), $pluginInfo) : parent::getProfileHintDesc();
    }

    /**
     * {@inheritdoc}
     */
    public function getProfileHintReturnPolicy()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getProfileHintReturnPolicy');
        return $pluginInfo ? $this->___callPlugins('getProfileHintReturnPolicy', func_get_args(), $pluginInfo) : parent::getProfileHintReturnPolicy();
    }

    /**
     * {@inheritdoc}
     */
    public function getProfileHintShippingPolicy()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getProfileHintShippingPolicy');
        return $pluginInfo ? $this->___callPlugins('getProfileHintShippingPolicy', func_get_args(), $pluginInfo) : parent::getProfileHintShippingPolicy();
    }

    /**
     * {@inheritdoc}
     */
    public function getProfileHintCountry()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getProfileHintCountry');
        return $pluginInfo ? $this->___callPlugins('getProfileHintCountry', func_get_args(), $pluginInfo) : parent::getProfileHintCountry();
    }

    /**
     * {@inheritdoc}
     */
    public function getProfileHintMeta()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getProfileHintMeta');
        return $pluginInfo ? $this->___callPlugins('getProfileHintMeta', func_get_args(), $pluginInfo) : parent::getProfileHintMeta();
    }

    /**
     * {@inheritdoc}
     */
    public function getProfileHintMetaDesc()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getProfileHintMetaDesc');
        return $pluginInfo ? $this->___callPlugins('getProfileHintMetaDesc', func_get_args(), $pluginInfo) : parent::getProfileHintMetaDesc();
    }

    /**
     * {@inheritdoc}
     */
    public function getProfileHintBank()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getProfileHintBank');
        return $pluginInfo ? $this->___callPlugins('getProfileHintBank', func_get_args(), $pluginInfo) : parent::getProfileHintBank();
    }

    /**
     * {@inheritdoc}
     */
    public function getProfileUrl()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getProfileUrl');
        return $pluginInfo ? $this->___callPlugins('getProfileUrl', func_get_args(), $pluginInfo) : parent::getProfileUrl();
    }

    /**
     * {@inheritdoc}
     */
    public function getCollectionUrl()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getCollectionUrl');
        return $pluginInfo ? $this->___callPlugins('getCollectionUrl', func_get_args(), $pluginInfo) : parent::getCollectionUrl();
    }

    /**
     * {@inheritdoc}
     */
    public function getLocationUrl()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getLocationUrl');
        return $pluginInfo ? $this->___callPlugins('getLocationUrl', func_get_args(), $pluginInfo) : parent::getLocationUrl();
    }

    /**
     * {@inheritdoc}
     */
    public function getFeedbackUrl()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getFeedbackUrl');
        return $pluginInfo ? $this->___callPlugins('getFeedbackUrl', func_get_args(), $pluginInfo) : parent::getFeedbackUrl();
    }

    /**
     * {@inheritdoc}
     */
    public function getRewriteUrl($targetUrl)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getRewriteUrl');
        return $pluginInfo ? $this->___callPlugins('getRewriteUrl', func_get_args(), $pluginInfo) : parent::getRewriteUrl($targetUrl);
    }

    /**
     * {@inheritdoc}
     */
    public function getRewriteUrlPath($targetUrl)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getRewriteUrlPath');
        return $pluginInfo ? $this->___callPlugins('getRewriteUrlPath', func_get_args(), $pluginInfo) : parent::getRewriteUrlPath($targetUrl);
    }

    /**
     * {@inheritdoc}
     */
    public function getTargetUrlPath()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getTargetUrlPath');
        return $pluginInfo ? $this->___callPlugins('getTargetUrlPath', func_get_args(), $pluginInfo) : parent::getTargetUrlPath();
    }

    /**
     * {@inheritdoc}
     */
    public function getPlaceholderImage()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getPlaceholderImage');
        return $pluginInfo ? $this->___callPlugins('getPlaceholderImage', func_get_args(), $pluginInfo) : parent::getPlaceholderImage();
    }

    /**
     * {@inheritdoc}
     */
    public function getSellerProCount($sellerId)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getSellerProCount');
        return $pluginInfo ? $this->___callPlugins('getSellerProCount', func_get_args(), $pluginInfo) : parent::getSellerProCount($sellerId);
    }

    /**
     * {@inheritdoc}
     */
    public function getMediaUrl()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getMediaUrl');
        return $pluginInfo ? $this->___callPlugins('getMediaUrl', func_get_args(), $pluginInfo) : parent::getMediaUrl();
    }

    /**
     * {@inheritdoc}
     */
    public function getMaxDownloads()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getMaxDownloads');
        return $pluginInfo ? $this->___callPlugins('getMaxDownloads', func_get_args(), $pluginInfo) : parent::getMaxDownloads();
    }

    /**
     * {@inheritdoc}
     */
    public function getConfigPriceWebsiteScope()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getConfigPriceWebsiteScope');
        return $pluginInfo ? $this->___callPlugins('getConfigPriceWebsiteScope', func_get_args(), $pluginInfo) : parent::getConfigPriceWebsiteScope();
    }

    /**
     * {@inheritdoc}
     */
    public function getSkuType()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getSkuType');
        return $pluginInfo ? $this->___callPlugins('getSkuType', func_get_args(), $pluginInfo) : parent::getSkuType();
    }

    /**
     * {@inheritdoc}
     */
    public function getSkuPrefix()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getSkuPrefix');
        return $pluginInfo ? $this->___callPlugins('getSkuPrefix', func_get_args(), $pluginInfo) : parent::getSkuPrefix();
    }

    /**
     * {@inheritdoc}
     */
    public function getSellerProfileDisplayFlag()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getSellerProfileDisplayFlag');
        return $pluginInfo ? $this->___callPlugins('getSellerProfileDisplayFlag', func_get_args(), $pluginInfo) : parent::getSellerProfileDisplayFlag();
    }

    /**
     * {@inheritdoc}
     */
    public function getAutomaticUrlRewrite()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getAutomaticUrlRewrite');
        return $pluginInfo ? $this->___callPlugins('getAutomaticUrlRewrite', func_get_args(), $pluginInfo) : parent::getAutomaticUrlRewrite();
    }

    /**
     * {@inheritdoc}
     */
    public function getYouTubeApiKey()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getYouTubeApiKey');
        return $pluginInfo ? $this->___callPlugins('getYouTubeApiKey', func_get_args(), $pluginInfo) : parent::getYouTubeApiKey();
    }

    /**
     * {@inheritdoc}
     */
    public function getCustomerAccountRedirect()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getCustomerAccountRedirect');
        return $pluginInfo ? $this->___callPlugins('getCustomerAccountRedirect', func_get_args(), $pluginInfo) : parent::getCustomerAccountRedirect();
    }

    /**
     * {@inheritdoc}
     */
    public function getAllowedControllersBySetData($allowedModule)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getAllowedControllersBySetData');
        return $pluginInfo ? $this->___callPlugins('getAllowedControllersBySetData', func_get_args(), $pluginInfo) : parent::getAllowedControllersBySetData($allowedModule);
    }

    /**
     * {@inheritdoc}
     */
    public function isSellerGroupModuleInstalled()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'isSellerGroupModuleInstalled');
        return $pluginInfo ? $this->___callPlugins('isSellerGroupModuleInstalled', func_get_args(), $pluginInfo) : parent::isSellerGroupModuleInstalled();
    }

    /**
     * {@inheritdoc}
     */
    public function isAllowedAction($actionName = '')
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'isAllowedAction');
        return $pluginInfo ? $this->___callPlugins('isAllowedAction', func_get_args(), $pluginInfo) : parent::isAllowedAction($actionName);
    }

    /**
     * {@inheritdoc}
     */
    public function getPageLayout()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getPageLayout');
        return $pluginInfo ? $this->___callPlugins('getPageLayout', func_get_args(), $pluginInfo) : parent::getPageLayout();
    }

    /**
     * {@inheritdoc}
     */
    public function getDisplayBannerLayout2()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getDisplayBannerLayout2');
        return $pluginInfo ? $this->___callPlugins('getDisplayBannerLayout2', func_get_args(), $pluginInfo) : parent::getDisplayBannerLayout2();
    }

    /**
     * {@inheritdoc}
     */
    public function getBannerImageLayout2()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getBannerImageLayout2');
        return $pluginInfo ? $this->___callPlugins('getBannerImageLayout2', func_get_args(), $pluginInfo) : parent::getBannerImageLayout2();
    }

    /**
     * {@inheritdoc}
     */
    public function getBannerContentLayout2()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getBannerContentLayout2');
        return $pluginInfo ? $this->___callPlugins('getBannerContentLayout2', func_get_args(), $pluginInfo) : parent::getBannerContentLayout2();
    }

    /**
     * {@inheritdoc}
     */
    public function getBannerButtonLayout2()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getBannerButtonLayout2');
        return $pluginInfo ? $this->___callPlugins('getBannerButtonLayout2', func_get_args(), $pluginInfo) : parent::getBannerButtonLayout2();
    }

    /**
     * {@inheritdoc}
     */
    public function getSellerProfileLayout()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getSellerProfileLayout');
        return $pluginInfo ? $this->___callPlugins('getSellerProfileLayout', func_get_args(), $pluginInfo) : parent::getSellerProfileLayout();
    }

    /**
     * {@inheritdoc}
     */
    public function getTermsConditionUrlLayout2()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getTermsConditionUrlLayout2');
        return $pluginInfo ? $this->___callPlugins('getTermsConditionUrlLayout2', func_get_args(), $pluginInfo) : parent::getTermsConditionUrlLayout2();
    }

    /**
     * {@inheritdoc}
     */
    public function getDisplayBannerLayout3()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getDisplayBannerLayout3');
        return $pluginInfo ? $this->___callPlugins('getDisplayBannerLayout3', func_get_args(), $pluginInfo) : parent::getDisplayBannerLayout3();
    }

    /**
     * {@inheritdoc}
     */
    public function getBannerImageLayout3()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getBannerImageLayout3');
        return $pluginInfo ? $this->___callPlugins('getBannerImageLayout3', func_get_args(), $pluginInfo) : parent::getBannerImageLayout3();
    }

    /**
     * {@inheritdoc}
     */
    public function getBannerContentLayout3()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getBannerContentLayout3');
        return $pluginInfo ? $this->___callPlugins('getBannerContentLayout3', func_get_args(), $pluginInfo) : parent::getBannerContentLayout3();
    }

    /**
     * {@inheritdoc}
     */
    public function getBannerButtonLayout3()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getBannerButtonLayout3');
        return $pluginInfo ? $this->___callPlugins('getBannerButtonLayout3', func_get_args(), $pluginInfo) : parent::getBannerButtonLayout3();
    }

    /**
     * {@inheritdoc}
     */
    public function getTermsConditionUrlLayout3()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getTermsConditionUrlLayout3');
        return $pluginInfo ? $this->___callPlugins('getTermsConditionUrlLayout3', func_get_args(), $pluginInfo) : parent::getTermsConditionUrlLayout3();
    }

    /**
     * {@inheritdoc}
     */
    public function getDisplayIconLayout3()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getDisplayIconLayout3');
        return $pluginInfo ? $this->___callPlugins('getDisplayIconLayout3', func_get_args(), $pluginInfo) : parent::getDisplayIconLayout3();
    }

    /**
     * {@inheritdoc}
     */
    public function getIconImage1Layout3()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getIconImage1Layout3');
        return $pluginInfo ? $this->___callPlugins('getIconImage1Layout3', func_get_args(), $pluginInfo) : parent::getIconImage1Layout3();
    }

    /**
     * {@inheritdoc}
     */
    public function getIconImageLabel1Layout3()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getIconImageLabel1Layout3');
        return $pluginInfo ? $this->___callPlugins('getIconImageLabel1Layout3', func_get_args(), $pluginInfo) : parent::getIconImageLabel1Layout3();
    }

    /**
     * {@inheritdoc}
     */
    public function getIconImage2Layout3()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getIconImage2Layout3');
        return $pluginInfo ? $this->___callPlugins('getIconImage2Layout3', func_get_args(), $pluginInfo) : parent::getIconImage2Layout3();
    }

    /**
     * {@inheritdoc}
     */
    public function getIconImageLabel2Layout3()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getIconImageLabel2Layout3');
        return $pluginInfo ? $this->___callPlugins('getIconImageLabel2Layout3', func_get_args(), $pluginInfo) : parent::getIconImageLabel2Layout3();
    }

    /**
     * {@inheritdoc}
     */
    public function getIconImage3Layout3()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getIconImage3Layout3');
        return $pluginInfo ? $this->___callPlugins('getIconImage3Layout3', func_get_args(), $pluginInfo) : parent::getIconImage3Layout3();
    }

    /**
     * {@inheritdoc}
     */
    public function getIconImageLabel3Layout3()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getIconImageLabel3Layout3');
        return $pluginInfo ? $this->___callPlugins('getIconImageLabel3Layout3', func_get_args(), $pluginInfo) : parent::getIconImageLabel3Layout3();
    }

    /**
     * {@inheritdoc}
     */
    public function getIconImage4Layout3()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getIconImage4Layout3');
        return $pluginInfo ? $this->___callPlugins('getIconImage4Layout3', func_get_args(), $pluginInfo) : parent::getIconImage4Layout3();
    }

    /**
     * {@inheritdoc}
     */
    public function getIconImageLabel4Layout3()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getIconImageLabel4Layout3');
        return $pluginInfo ? $this->___callPlugins('getIconImageLabel4Layout3', func_get_args(), $pluginInfo) : parent::getIconImageLabel4Layout3();
    }

    /**
     * {@inheritdoc}
     */
    public function getIconImage5Layout3()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getIconImage5Layout3');
        return $pluginInfo ? $this->___callPlugins('getIconImage5Layout3', func_get_args(), $pluginInfo) : parent::getIconImage5Layout3();
    }

    /**
     * {@inheritdoc}
     */
    public function getIconImageLabel5Layout3()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getIconImageLabel5Layout3');
        return $pluginInfo ? $this->___callPlugins('getIconImageLabel5Layout3', func_get_args(), $pluginInfo) : parent::getIconImageLabel5Layout3();
    }

    /**
     * {@inheritdoc}
     */
    public function getMarketplacelabel1Layout3()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getMarketplacelabel1Layout3');
        return $pluginInfo ? $this->___callPlugins('getMarketplacelabel1Layout3', func_get_args(), $pluginInfo) : parent::getMarketplacelabel1Layout3();
    }

    /**
     * {@inheritdoc}
     */
    public function getMarketplacelabel2Layout3()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getMarketplacelabel2Layout3');
        return $pluginInfo ? $this->___callPlugins('getMarketplacelabel2Layout3', func_get_args(), $pluginInfo) : parent::getMarketplacelabel2Layout3();
    }

    /**
     * {@inheritdoc}
     */
    public function getMarketplacelabel3Layout3()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getMarketplacelabel3Layout3');
        return $pluginInfo ? $this->___callPlugins('getMarketplacelabel3Layout3', func_get_args(), $pluginInfo) : parent::getMarketplacelabel3Layout3();
    }

    /**
     * {@inheritdoc}
     */
    public function getOrderApprovalRequired()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getOrderApprovalRequired');
        return $pluginInfo ? $this->___callPlugins('getOrderApprovalRequired', func_get_args(), $pluginInfo) : parent::getOrderApprovalRequired();
    }

    /**
     * {@inheritdoc}
     */
    public function getAllowProductLimit()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getAllowProductLimit');
        return $pluginInfo ? $this->___callPlugins('getAllowProductLimit', func_get_args(), $pluginInfo) : parent::getAllowProductLimit();
    }

    /**
     * {@inheritdoc}
     */
    public function getGlobalProductLimitQty()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getGlobalProductLimitQty');
        return $pluginInfo ? $this->___callPlugins('getGlobalProductLimitQty', func_get_args(), $pluginInfo) : parent::getGlobalProductLimitQty();
    }

    /**
     * {@inheritdoc}
     */
    public function getOrderedPricebyorder($order, $price)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getOrderedPricebyorder');
        return $pluginInfo ? $this->___callPlugins('getOrderedPricebyorder', func_get_args(), $pluginInfo) : parent::getOrderedPricebyorder($order, $price);
    }

    /**
     * {@inheritdoc}
     */
    public function isSellerCouponModuleInstalled()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'isSellerCouponModuleInstalled');
        return $pluginInfo ? $this->___callPlugins('isSellerCouponModuleInstalled', func_get_args(), $pluginInfo) : parent::isSellerCouponModuleInstalled();
    }

    /**
     * {@inheritdoc}
     */
    public function getCustomerSharePerWebsite()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getCustomerSharePerWebsite');
        return $pluginInfo ? $this->___callPlugins('getCustomerSharePerWebsite', func_get_args(), $pluginInfo) : parent::getCustomerSharePerWebsite();
    }

    /**
     * {@inheritdoc}
     */
    public function isMpcashondeliveryModuleInstalled()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'isMpcashondeliveryModuleInstalled');
        return $pluginInfo ? $this->___callPlugins('isMpcashondeliveryModuleInstalled', func_get_args(), $pluginInfo) : parent::isMpcashondeliveryModuleInstalled();
    }

    /**
     * {@inheritdoc}
     */
    public function getFormatedPrice($price = 0)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getFormatedPrice');
        return $pluginInfo ? $this->___callPlugins('getFormatedPrice', func_get_args(), $pluginInfo) : parent::getFormatedPrice($price);
    }

    /**
     * {@inheritdoc}
     */
    public function getIsSeparatePanel()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getIsSeparatePanel');
        return $pluginInfo ? $this->___callPlugins('getIsSeparatePanel', func_get_args(), $pluginInfo) : parent::getIsSeparatePanel();
    }

    /**
     * {@inheritdoc}
     */
    public function getIsAdminViewCategoryTree()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getIsAdminViewCategoryTree');
        return $pluginInfo ? $this->___callPlugins('getIsAdminViewCategoryTree', func_get_args(), $pluginInfo) : parent::getIsAdminViewCategoryTree();
    }

    /**
     * {@inheritdoc}
     */
    public function getControllerMappedPermissions()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getControllerMappedPermissions');
        return $pluginInfo ? $this->___callPlugins('getControllerMappedPermissions', func_get_args(), $pluginInfo) : parent::getControllerMappedPermissions();
    }

    /**
     * {@inheritdoc}
     */
    public function isMpSellerProductSearchModuleInstalled()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'isMpSellerProductSearchModuleInstalled');
        return $pluginInfo ? $this->___callPlugins('isMpSellerProductSearchModuleInstalled', func_get_args(), $pluginInfo) : parent::isMpSellerProductSearchModuleInstalled();
    }

    /**
     * {@inheritdoc}
     */
    public function getImageSize($image)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getImageSize');
        return $pluginInfo ? $this->___callPlugins('getImageSize', func_get_args(), $pluginInfo) : parent::getImageSize($image);
    }

    /**
     * {@inheritdoc}
     */
    public function isSellerSliderModuleInstalled()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'isSellerSliderModuleInstalled');
        return $pluginInfo ? $this->___callPlugins('isSellerSliderModuleInstalled', func_get_args(), $pluginInfo) : parent::isSellerSliderModuleInstalled();
    }

    /**
     * {@inheritdoc}
     */
    public function validateXssString($value = null)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'validateXssString');
        return $pluginInfo ? $this->___callPlugins('validateXssString', func_get_args(), $pluginInfo) : parent::validateXssString($value);
    }

    /**
     * {@inheritdoc}
     */
    public function getSellerDashboardLogoUrl()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getSellerDashboardLogoUrl');
        return $pluginInfo ? $this->___callPlugins('getSellerDashboardLogoUrl', func_get_args(), $pluginInfo) : parent::getSellerDashboardLogoUrl();
    }

    /**
     * {@inheritdoc}
     */
    public function clearCache()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'clearCache');
        return $pluginInfo ? $this->___callPlugins('clearCache', func_get_args(), $pluginInfo) : parent::clearCache();
    }

    /**
     * {@inheritdoc}
     */
    public function getWeightUnit()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getWeightUnit');
        return $pluginInfo ? $this->___callPlugins('getWeightUnit', func_get_args(), $pluginInfo) : parent::getWeightUnit();
    }

    /**
     * {@inheritdoc}
     */
    public function getCurrentCurrencyPrice($currencyRate, $basePrice)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getCurrentCurrencyPrice');
        return $pluginInfo ? $this->___callPlugins('getCurrentCurrencyPrice', func_get_args(), $pluginInfo) : parent::getCurrentCurrencyPrice($currencyRate, $basePrice);
    }

    /**
     * {@inheritdoc}
     */
    public function getSellerRegistrationUrl()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getSellerRegistrationUrl');
        return $pluginInfo ? $this->___callPlugins('getSellerRegistrationUrl', func_get_args(), $pluginInfo) : parent::getSellerRegistrationUrl();
    }

    /**
     * {@inheritdoc}
     */
    public function isProfileCompleted()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'isProfileCompleted');
        return $pluginInfo ? $this->___callPlugins('isProfileCompleted', func_get_args(), $pluginInfo) : parent::isProfileCompleted();
    }

    /**
     * {@inheritdoc}
     */
    public function getRequestVar()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getRequestVar');
        return $pluginInfo ? $this->___callPlugins('getRequestVar', func_get_args(), $pluginInfo) : parent::getRequestVar();
    }

    /**
     * {@inheritdoc}
     */
    public function isSellerFilterActive()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'isSellerFilterActive');
        return $pluginInfo ? $this->___callPlugins('isSellerFilterActive', func_get_args(), $pluginInfo) : parent::isSellerFilterActive();
    }

    /**
     * {@inheritdoc}
     */
    public function getSellerInfo($sellerId)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getSellerInfo');
        return $pluginInfo ? $this->___callPlugins('getSellerInfo', func_get_args(), $pluginInfo) : parent::getSellerInfo($sellerId);
    }

    /**
     * {@inheritdoc}
     */
    public function getSellerProductCollection($sellerId, $productId = 0, $productCount = 0)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getSellerProductCollection');
        return $pluginInfo ? $this->___callPlugins('getSellerProductCollection', func_get_args(), $pluginInfo) : parent::getSellerProductCollection($sellerId, $productId, $productCount);
    }

    /**
     * {@inheritdoc}
     */
    public function getDisplayCardType()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getDisplayCardType');
        return $pluginInfo ? $this->___callPlugins('getDisplayCardType', func_get_args(), $pluginInfo) : parent::getDisplayCardType();
    }

    /**
     * {@inheritdoc}
     */
    public function getImageUrl($product, $imageType = 'product_page_image_small')
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getImageUrl');
        return $pluginInfo ? $this->___callPlugins('getImageUrl', func_get_args(), $pluginInfo) : parent::getImageUrl($product, $imageType);
    }

    /**
     * {@inheritdoc}
     */
    public function allowSellerFilter()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'allowSellerFilter');
        return $pluginInfo ? $this->___callPlugins('allowSellerFilter', func_get_args(), $pluginInfo) : parent::allowSellerFilter();
    }

    /**
     * {@inheritdoc}
     */
    public function getAdminFilterDisplayName()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getAdminFilterDisplayName');
        return $pluginInfo ? $this->___callPlugins('getAdminFilterDisplayName', func_get_args(), $pluginInfo) : parent::getAdminFilterDisplayName();
    }

    /**
     * {@inheritdoc}
     */
    public function allowProductInCart()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'allowProductInCart');
        return $pluginInfo ? $this->___callPlugins('allowProductInCart', func_get_args(), $pluginInfo) : parent::allowProductInCart();
    }

    /**
     * {@inheritdoc}
     */
    public function getProfileDetail($type = 'profile')
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getProfileDetail');
        return $pluginInfo ? $this->___callPlugins('getProfileDetail', func_get_args(), $pluginInfo) : parent::getProfileDetail($type);
    }

    /**
     * {@inheritdoc}
     */
    public function getUrlRewriteCollection()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getUrlRewriteCollection');
        return $pluginInfo ? $this->___callPlugins('getUrlRewriteCollection', func_get_args(), $pluginInfo) : parent::getUrlRewriteCollection();
    }

    /**
     * {@inheritdoc}
     */
    public function getParam($key)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getParam');
        return $pluginInfo ? $this->___callPlugins('getParam', func_get_args(), $pluginInfo) : parent::getParam($key);
    }

    /**
     * {@inheritdoc}
     */
    public function isWysiwygEnabled()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'isWysiwygEnabled');
        return $pluginInfo ? $this->___callPlugins('isWysiwygEnabled', func_get_args(), $pluginInfo) : parent::isWysiwygEnabled();
    }

    /**
     * {@inheritdoc}
     */
    public function getAdminName()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getAdminName');
        return $pluginInfo ? $this->___callPlugins('getAdminName', func_get_args(), $pluginInfo) : parent::getAdminName();
    }

    /**
     * {@inheritdoc}
     */
    public function joinCustomer($collection)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'joinCustomer');
        return $pluginInfo ? $this->___callPlugins('joinCustomer', func_get_args(), $pluginInfo) : parent::joinCustomer($collection);
    }

    /**
     * {@inheritdoc}
     */
    public function getSellerCollection()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getSellerCollection');
        return $pluginInfo ? $this->___callPlugins('getSellerCollection', func_get_args(), $pluginInfo) : parent::getSellerCollection();
    }

    /**
     * {@inheritdoc}
     */
    public function includeSellerUrlInSitemap()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'includeSellerUrlInSitemap');
        return $pluginInfo ? $this->___callPlugins('includeSellerUrlInSitemap', func_get_args(), $pluginInfo) : parent::includeSellerUrlInSitemap();
    }

    /**
     * {@inheritdoc}
     */
    public function includeProfileUrlInSitemap()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'includeProfileUrlInSitemap');
        return $pluginInfo ? $this->___callPlugins('includeProfileUrlInSitemap', func_get_args(), $pluginInfo) : parent::includeProfileUrlInSitemap();
    }

    /**
     * {@inheritdoc}
     */
    public function getFrequencyOfProfileUrlInSitemap()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getFrequencyOfProfileUrlInSitemap');
        return $pluginInfo ? $this->___callPlugins('getFrequencyOfProfileUrlInSitemap', func_get_args(), $pluginInfo) : parent::getFrequencyOfProfileUrlInSitemap();
    }

    /**
     * {@inheritdoc}
     */
    public function getPriorityOfProfileUrlInSitemap()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getPriorityOfProfileUrlInSitemap');
        return $pluginInfo ? $this->___callPlugins('getPriorityOfProfileUrlInSitemap', func_get_args(), $pluginInfo) : parent::getPriorityOfProfileUrlInSitemap();
    }

    /**
     * {@inheritdoc}
     */
    public function includeCollectionUrlInSitemap()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'includeCollectionUrlInSitemap');
        return $pluginInfo ? $this->___callPlugins('includeCollectionUrlInSitemap', func_get_args(), $pluginInfo) : parent::includeCollectionUrlInSitemap();
    }

    /**
     * {@inheritdoc}
     */
    public function getFrequencyOfCollectionUrlInSitemap()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getFrequencyOfCollectionUrlInSitemap');
        return $pluginInfo ? $this->___callPlugins('getFrequencyOfCollectionUrlInSitemap', func_get_args(), $pluginInfo) : parent::getFrequencyOfCollectionUrlInSitemap();
    }

    /**
     * {@inheritdoc}
     */
    public function getPriorityOfCollectionUrlInSitemap()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getPriorityOfCollectionUrlInSitemap');
        return $pluginInfo ? $this->___callPlugins('getPriorityOfCollectionUrlInSitemap', func_get_args(), $pluginInfo) : parent::getPriorityOfCollectionUrlInSitemap();
    }

    /**
     * {@inheritdoc}
     */
    public function isAllowed($action)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'isAllowed');
        return $pluginInfo ? $this->___callPlugins('isAllowed', func_get_args(), $pluginInfo) : parent::isAllowed($action);
    }

    /**
     * {@inheritdoc}
     */
    public function isFlatProductEnable()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'isFlatProductEnable');
        return $pluginInfo ? $this->___callPlugins('isFlatProductEnable', func_get_args(), $pluginInfo) : parent::isFlatProductEnable();
    }

    /**
     * {@inheritdoc}
     */
    public function isFlatCategoryEnable()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'isFlatCategoryEnable');
        return $pluginInfo ? $this->___callPlugins('isFlatCategoryEnable', func_get_args(), $pluginInfo) : parent::isFlatCategoryEnable();
    }

    /**
     * {@inheritdoc}
     */
    public function reIndexData()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'reIndexData');
        return $pluginInfo ? $this->___callPlugins('reIndexData', func_get_args(), $pluginInfo) : parent::reIndexData();
    }

    /**
     * {@inheritdoc}
     */
    public function getSellerFlagData($field)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getSellerFlagData');
        return $pluginInfo ? $this->___callPlugins('getSellerFlagData', func_get_args(), $pluginInfo) : parent::getSellerFlagData($field);
    }

    /**
     * {@inheritdoc}
     */
    public function getSellerFlagStatus()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getSellerFlagStatus');
        return $pluginInfo ? $this->___callPlugins('getSellerFlagStatus', func_get_args(), $pluginInfo) : parent::getSellerFlagStatus();
    }

    /**
     * {@inheritdoc}
     */
    public function getProductFlagData($field)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getProductFlagData');
        return $pluginInfo ? $this->___callPlugins('getProductFlagData', func_get_args(), $pluginInfo) : parent::getProductFlagData($field);
    }

    /**
     * {@inheritdoc}
     */
    public function getProductFlagStatus()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getProductFlagStatus');
        return $pluginInfo ? $this->___callPlugins('getProductFlagStatus', func_get_args(), $pluginInfo) : parent::getProductFlagStatus();
    }

    /**
     * {@inheritdoc}
     */
    public function logDataInLogger($data)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'logDataInLogger');
        return $pluginInfo ? $this->___callPlugins('logDataInLogger', func_get_args(), $pluginInfo) : parent::logDataInLogger($data);
    }

    /**
     * {@inheritdoc}
     */
    public function getSellerIdByProductId($productId = '')
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getSellerIdByProductId');
        return $pluginInfo ? $this->___callPlugins('getSellerIdByProductId', func_get_args(), $pluginInfo) : parent::getSellerIdByProductId($productId);
    }

    /**
     * {@inheritdoc}
     */
    public function getSellerList()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getSellerList');
        return $pluginInfo ? $this->___callPlugins('getSellerList', func_get_args(), $pluginInfo) : parent::getSellerList();
    }

    /**
     * {@inheritdoc}
     */
    public function getParams()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getParams');
        return $pluginInfo ? $this->___callPlugins('getParams', func_get_args(), $pluginInfo) : parent::getParams();
    }

    /**
     * {@inheritdoc}
     */
    public function getMinOrderSettings() : bool
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getMinOrderSettings');
        return $pluginInfo ? $this->___callPlugins('getMinOrderSettings', func_get_args(), $pluginInfo) : parent::getMinOrderSettings();
    }

    /**
     * {@inheritdoc}
     */
    public function getAnalyticStatus() : bool
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getAnalyticStatus');
        return $pluginInfo ? $this->___callPlugins('getAnalyticStatus', func_get_args(), $pluginInfo) : parent::getAnalyticStatus();
    }

    /**
     * {@inheritdoc}
     */
    public function getMinOrderAmount() : float
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getMinOrderAmount');
        return $pluginInfo ? $this->___callPlugins('getMinOrderAmount', func_get_args(), $pluginInfo) : parent::getMinOrderAmount();
    }

    /**
     * {@inheritdoc}
     */
    public function getMinAmountForSeller() : bool
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getMinAmountForSeller');
        return $pluginInfo ? $this->___callPlugins('getMinAmountForSeller', func_get_args(), $pluginInfo) : parent::getMinAmountForSeller();
    }

    /**
     * {@inheritdoc}
     */
    public function getSellerItemsDetails($items)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getSellerItemsDetails');
        return $pluginInfo ? $this->___callPlugins('getSellerItemsDetails', func_get_args(), $pluginInfo) : parent::getSellerItemsDetails($items);
    }

    /**
     * {@inheritdoc}
     */
    public function getSellerId($mpassignproductId, $proid)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getSellerId');
        return $pluginInfo ? $this->___callPlugins('getSellerId', func_get_args(), $pluginInfo) : parent::getSellerId($mpassignproductId, $proid);
    }

    /**
     * {@inheritdoc}
     */
    public function getSellerProducts($sellerId)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getSellerProducts');
        return $pluginInfo ? $this->___callPlugins('getSellerProducts', func_get_args(), $pluginInfo) : parent::getSellerProducts($sellerId);
    }

    /**
     * {@inheritdoc}
     */
    public function getSellerAnalyticId($sellerId = 0)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getSellerAnalyticId');
        return $pluginInfo ? $this->___callPlugins('getSellerAnalyticId', func_get_args(), $pluginInfo) : parent::getSellerAnalyticId($sellerId);
    }

    /**
     * {@inheritdoc}
     */
    public function getProfileBannerImage()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getProfileBannerImage');
        return $pluginInfo ? $this->___callPlugins('getProfileBannerImage', func_get_args(), $pluginInfo) : parent::getProfileBannerImage();
    }

    /**
     * {@inheritdoc}
     */
    public function checkIfSellerAttribute($attributeId)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'checkIfSellerAttribute');
        return $pluginInfo ? $this->___callPlugins('checkIfSellerAttribute', func_get_args(), $pluginInfo) : parent::checkIfSellerAttribute($attributeId);
    }

    /**
     * {@inheritdoc}
     */
    public function getAttrOwnershipDetails($attributeId)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getAttrOwnershipDetails');
        return $pluginInfo ? $this->___callPlugins('getAttrOwnershipDetails', func_get_args(), $pluginInfo) : parent::getAttrOwnershipDetails($attributeId);
    }

    /**
     * {@inheritdoc}
     */
    public function getConfigurationValue($path = '')
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getConfigurationValue');
        return $pluginInfo ? $this->___callPlugins('getConfigurationValue', func_get_args(), $pluginInfo) : parent::getConfigurationValue($path);
    }

    /**
     * {@inheritdoc}
     */
    public function getDriverFile()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getDriverFile');
        return $pluginInfo ? $this->___callPlugins('getDriverFile', func_get_args(), $pluginInfo) : parent::getDriverFile();
    }

    /**
     * {@inheritdoc}
     */
    public function getSeparatePanelOptions()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getSeparatePanelOptions');
        return $pluginInfo ? $this->___callPlugins('getSeparatePanelOptions', func_get_args(), $pluginInfo) : parent::getSeparatePanelOptions();
    }

    /**
     * {@inheritdoc}
     */
    public function buildUrl($targetUrl, $params = [])
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'buildUrl');
        return $pluginInfo ? $this->___callPlugins('buildUrl', func_get_args(), $pluginInfo) : parent::buildUrl($targetUrl, $params);
    }

    /**
     * {@inheritdoc}
     */
    public function arrayToJson($data)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'arrayToJson');
        return $pluginInfo ? $this->___callPlugins('arrayToJson', func_get_args(), $pluginInfo) : parent::arrayToJson($data);
    }

    /**
     * {@inheritdoc}
     */
    public function jsonToArray($data)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'jsonToArray');
        return $pluginInfo ? $this->___callPlugins('jsonToArray', func_get_args(), $pluginInfo) : parent::jsonToArray($data);
    }

    /**
     * {@inheritdoc}
     */
    public function getObjectOfClass($class)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getObjectOfClass');
        return $pluginInfo ? $this->___callPlugins('getObjectOfClass', func_get_args(), $pluginInfo) : parent::getObjectOfClass($class);
    }

    /**
     * {@inheritdoc}
     */
    public function updateAnalyticId($sellerId, $analyticId)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'updateAnalyticId');
        return $pluginInfo ? $this->___callPlugins('updateAnalyticId', func_get_args(), $pluginInfo) : parent::updateAnalyticId($sellerId, $analyticId);
    }

    /**
     * {@inheritdoc}
     */
    public function getRequest()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getRequest');
        return $pluginInfo ? $this->___callPlugins('getRequest', func_get_args(), $pluginInfo) : parent::getRequest();
    }

    /**
     * {@inheritdoc}
     */
    public function getSellerOrderStatus($order, $sellerId = 0)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getSellerOrderStatus');
        return $pluginInfo ? $this->___callPlugins('getSellerOrderStatus', func_get_args(), $pluginInfo) : parent::getSellerOrderStatus($order, $sellerId);
    }

    /**
     * {@inheritdoc}
     */
    public function getSellerIdByOrderItem($item)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getSellerIdByOrderItem');
        return $pluginInfo ? $this->___callPlugins('getSellerIdByOrderItem', func_get_args(), $pluginInfo) : parent::getSellerIdByOrderItem($item);
    }

    /**
     * {@inheritdoc}
     */
    public function getQtyToShipCount($item)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getQtyToShipCount');
        return $pluginInfo ? $this->___callPlugins('getQtyToShipCount', func_get_args(), $pluginInfo) : parent::getQtyToShipCount($item);
    }

    /**
     * {@inheritdoc}
     */
    public function getQtyToRefundCount($item)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getQtyToRefundCount');
        return $pluginInfo ? $this->___callPlugins('getQtyToRefundCount', func_get_args(), $pluginInfo) : parent::getQtyToRefundCount($item);
    }

    /**
     * {@inheritdoc}
     */
    public function getSellerAllowedProduct()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getSellerAllowedProduct');
        return $pluginInfo ? $this->___callPlugins('getSellerAllowedProduct', func_get_args(), $pluginInfo) : parent::getSellerAllowedProduct();
    }

    /**
     * {@inheritdoc}
     */
    public function isAllRefunded($sellerId, $orderId)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'isAllRefunded');
        return $pluginInfo ? $this->___callPlugins('isAllRefunded', func_get_args(), $pluginInfo) : parent::isAllRefunded($sellerId, $orderId);
    }

    /**
     * {@inheritdoc}
     */
    public function isModuleOutputEnabled($moduleName = null)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'isModuleOutputEnabled');
        return $pluginInfo ? $this->___callPlugins('isModuleOutputEnabled', func_get_args(), $pluginInfo) : parent::isModuleOutputEnabled($moduleName);
    }
}
