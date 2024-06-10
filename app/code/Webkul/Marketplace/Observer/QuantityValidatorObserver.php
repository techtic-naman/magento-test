<?php
/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_Marketplace
 * @author    Webkul Software Private Limited
 * @copyright Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */
namespace Webkul\Marketplace\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\CatalogInventory\Api\StockRegistryInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\CatalogInventory\Api\Data\StockItemInterface;
use Webkul\Marketplace\Model\Product;

class QuantityValidatorObserver implements ObserverInterface
{
    /**
     * @var StockRegistryInterface
     */
    protected $stockRegistry;
    /**
     * @var \Webkul\Marketplace\Helper\Data
     */
    protected $marketplaceHelperData;

    /**
     * Construct
     *
     * @param StockRegistryInterface $stockRegistry
     * @param \Webkul\Marketplace\Helper\Data $marketplaceHelperData
     */
    public function __construct(
        StockRegistryInterface $stockRegistry,
        \Webkul\Marketplace\Helper\Data $marketplaceHelperData
    ) {
        $this->stockRegistry = $stockRegistry;
        $this->marketplaceHelperData = $marketplaceHelperData;
    }

    /**
     * Marketplace Max quantity check in cart
     *
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $quoteItem = $observer->getEvent()->getItem();
        
        if (!$quoteItem ||
            !$quoteItem->getProductId() ||
            !$quoteItem->getQuote()
        ) {
            return;
        }
        $product = $quoteItem->getProduct();
        $qty = $quoteItem->getQty();
       
        /* @var \Magento\CatalogInventory\Model\Stock\Item $stockItem */
        $stockItem = $this->stockRegistry->getStockItem($product->getId(), $product->getStore()->getWebsiteId());
        if (!$stockItem instanceof StockItemInterface) {
            throw new LocalizedException(__('The Product stock item is invalid. Verify the stock item and try again.'));
        }
        $maxQty = $this->checkAndUpdateProductCartLimit($product);
        if ($maxQty && $qty > $maxQty) {
            throw new LocalizedException(
                __('The requested qty exceeds the maximum %1 qty allowed in shopping cart', $maxQty)
            );
        
        }
        $stockItem->setMaxSaleQty($maxQty)->save();
    }
    /**
     * Check And Update Product Cart Limit is used to check cart items limit
     *
     * @param  array $product
     * @return bool|float
     */
    public function checkAndUpdateProductCartLimit($product)
    {
        try {
            $allowProductLimit = $this->marketplaceHelperData->getAllowProductLimit();
            if ($allowProductLimit) {
                $sellerProductDataColl = $this->marketplaceHelperData->getSellerProductDataByProductId(
                    $product->getId()
                );
                if (count($sellerProductDataColl)) {
                    $productTypeId = $product['type_id'];
                    $productDownloadable = Product::PRODUCT_TYPE_DOWNLOADABLE;
                    $productVirtual = Product::PRODUCT_TYPE_VIRTUAL;
                    if ($productTypeId != $productDownloadable && $productTypeId != $productVirtual) {
                        $mpProductCartLimit = $product['mp_product_cart_limit'];
                        if (!$mpProductCartLimit) {
                            $mpProductCartLimit = $this->marketplaceHelperData->getGlobalProductLimitQty();
                        }
                        return $mpProductCartLimit;
                    }
                }
            }
        } catch (\Exception $e) {
            $this->marketplaceHelperData->logDataInLogger(
                "checkAndUpdateProductCartLimit : ".$e->getMessage()
            );
        }
        return false;
    }
}
