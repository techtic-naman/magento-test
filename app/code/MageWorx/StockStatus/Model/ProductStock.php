<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace MageWorx\StockStatus\Model;

use Magento\ConfigurableProduct\Model\Product\Type\Configurable;
use Magento\InventoryConfigurationApi\Api\GetStockItemConfigurationInterface;
use Magento\InventorySalesAdminUi\Model\ResourceModel\GetAssignedStockIdsBySku;
use Magento\InventorySalesApi\Api\GetProductSalableQtyInterface;
use MageWorx\StockStatus\Api\Data\StockStatusConfigReaderInterface;
use MageWorx\StockStatus\Model\Source\LowStockLevel;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\CatalogInventory\Model\Stock\Item;
use Magento\InventoryConfigurationApi\Model\IsSourceItemManagementAllowedForProductTypeInterface;
use Magento\InventorySalesApi\Model\GetAssignedStockIdForWebsiteInterface;
use Magento\GroupedProduct\Model\Product\Type\Grouped;
use Magento\Catalog\Model\Product\Type;

class ProductStock
{
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Store\Model\WebsiteRepository
     */
    protected $websiteRepository;

    /**
     * @var  StockStatusConfigReaderInterface
     */
    protected $stockStatusConfigReader;

    /**
     * @var Item
     */
    protected $stockItem;

    /**
     * @var IsSourceItemManagementAllowedForProductTypeInterface
     */
    protected $isManagementAllowed;

    /**
     * @var GetProductSalableQtyInterface
     */
    protected $getProductSalableQty;

    /**
     * @var GetAssignedStockIdForWebsiteInterface
     */
    protected $getAssignedStockIdForWebsite;

    /**
     * @var GetStockItemConfigurationInterface
     */
    protected $getStockItemConfiguration;

    /**
     * @var GetAssignedStockIdsBySku
     */
    protected $getAssignedStockIdsBySku;

    /**
     * ProductStock constructor.
     *
     * @param \Magento\Store\Model\WebsiteRepository $websiteRepository
     * @param IsSourceItemManagementAllowedForProductTypeInterface $isManagementAllowed
     * @param GetProductSalableQtyInterface $getProductSalableQty
     * @param GetAssignedStockIdForWebsiteInterface $getAssignedStockIdForWebsite
     * @param GetStockItemConfigurationInterface $getStockItemConfiguration
     * @param Item $stockItem
     * @param GetAssignedStockIdsBySku $getAssignedStockIdsBySku
     * @param StockStatusConfigReaderInterface $stockStatusConfigReader
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\Store\Model\WebsiteRepository $websiteRepository,
        IsSourceItemManagementAllowedForProductTypeInterface $isManagementAllowed,
        GetProductSalableQtyInterface $getProductSalableQty,
        GetAssignedStockIdForWebsiteInterface $getAssignedStockIdForWebsite,
        GetStockItemConfigurationInterface $getStockItemConfiguration,
        Item $stockItem,
        GetAssignedStockIdsBySku $getAssignedStockIdsBySku,
        StockStatusConfigReaderInterface $stockStatusConfigReader,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->websiteRepository            = $websiteRepository;
        $this->isManagementAllowed          = $isManagementAllowed;
        $this->getProductSalableQty         = $getProductSalableQty;
        $this->getAssignedStockIdForWebsite = $getAssignedStockIdForWebsite;
        $this->getStockItemConfiguration    = $getStockItemConfiguration;
        $this->stockItem                    = $stockItem;
        $this->getAssignedStockIdsBySku     = $getAssignedStockIdsBySku;
        $this->storeManager                 = $storeManager;
        $this->stockStatusConfigReader      = $stockStatusConfigReader;
    }

    /**
     * @param ProductInterface $product
     * @param array $attr
     * @return string
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\InventoryConfigurationApi\Exception\SkuIsNotAssignedToStockException
     */
    public function getProductStockText(ProductInterface $product, array $attr = []): string
    {
        if ($product->getTypeId() === Type::TYPE_BUNDLE) {
            $collection = $product->getTypeInstance(true)
                                  ->getSelectionsCollection(
                                      $product->getTypeInstance(true)->getOptionsIds($product),
                                      $product
                                  );

            $product = $this->chooseMinProduct($collection->getItems());
        } elseif ($product->getTypeId() === Grouped::TYPE_CODE) {
            $products = $product->getTypeInstance()->getAssociatedProducts($product);
            $product  = $this->chooseMinProduct($products);
        } elseif ($product->getTypeId() === Configurable::TYPE_CODE) {
            if (empty($attr)) {
                return '';
            }

            $product = $this->getProductForConfigurableType($product, $attr);
        }

        if ($product->getTypeId() !== Type::TYPE_SIMPLE) {
            return '';
        }

        $this->stockStatusConfigReader->setStoreId($this->storeManager->getStore()->getId());
        $productQty = $this->getProductQty($product);

        if ($productQty <= 0) {
            return '';
        }

        if ($this->stockStatusConfigReader->isDisplayUrgentStockMessage(
            ) && $productQty <= $this->stockStatusConfigReader->getUrgentStockValue()) {
            $text = $this->addStockToMessage($productQty, $this->stockStatusConfigReader->getUrgentStockMessage());

            return $this->decorateText($text, 'urgent');
        }

        if ($this->stockStatusConfigReader->isDisplayLowStockMessage() && $productQty <= $this->getProductLowValue(
                $product
            )) {
            $text = $this->addStockToMessage($productQty, $this->stockStatusConfigReader->getLowStockMessage());

            return $this->decorateText($text, 'low');
        }

        if ($this->stockStatusConfigReader->isDisplayInStockMessage()) {
            $text = $this->addStockToMessage($productQty, $this->stockStatusConfigReader->getInStockMessage());

            return $this->decorateText($text, 'default');
        }

        return '';
    }

    /**
     * @param int $productQty
     * @param string $message
     * @return string
     */
    protected function addStockToMessage(float $productQty, string $message): string
    {
        return str_replace('[stock]', (string)$productQty, $message);
    }

    /**
     * @param string $text
     * @param string $type
     * @return string
     */
    public function decorateText(string $text, string $type): string
    {
        switch ($type) {
            case 'low':
                $text = '<div class="mw-stock-status-text mw-stock-status-low">' . $text . '</div>' .
                    '<div class="mw-stock-status-low-bar"></div>';
                break;
            case 'urgent':
                $text = '<div class="mw-stock-status-text mw-stock-status-urgent">' . $text . '</div>' .
                    '<div class="mw-stock-status-urgent-bar"></div>';
                break;
            default:
                $text = '<div class="mw-stock-status-text mw-stock-status-default">' . $text . '</div>' .
                    '<div class="mw-stock-status-bar"></div>';
                break;
        }

        return $text;
    }

    /**
     * @param ProductInterface $product
     * @return float
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\InventoryConfigurationApi\Exception\SkuIsNotAssignedToStockException
     */
    public function getProductQty(ProductInterface $product): float
    {
        $productStockIds = $this->getAssignedStockIdsBySku->execute($product->getSku());
        $website         = $this->websiteRepository->getById($this->storeManager->getStore()->getWebsiteId());
        $stockId         = (int)$this->getAssignedStockIdForWebsite->execute($website->getCode());

        if ($this->isManagementAllowed->execute($product->getTypeId()) === true
            && array_search($stockId, $productStockIds) !== false
        ) {
            $isManageStock = $this->getStockItemConfiguration
                ->execute($product->getSku(), $stockId)
                ->isManageStock();
            if ($isManageStock) {
                $productQty = $this->getProductSalableQty->execute($product->getSku(), $stockId);

                return $productQty;
            }
        }

        //in case we can not get qty from MSI
        /** @var Item $stockItem */
        $stockItem  = $this->getProductStockItem($product);
        $productQty = $stockItem->getQty();

        return $productQty;
    }

    /**
     * @param ProductInterface $product
     * @return float
     */
    public function getProductLowValue(ProductInterface $product): float
    {
        $value = 0;
        switch ($this->stockStatusConfigReader->getLowStockLevel()) {
            case LowStockLevel::DEFAULT:
                /** @var Item $stockItem */
                $stockItem = $this->getProductStockItem($product);
                $value     = $stockItem->getNotifyStockQty();
                break;
            case LowStockLevel::CUSTOM:
                $value = $this->stockStatusConfigReader->getLowStockCustomValue();
                break;
        }

        return $value;
    }

    /**
     * @param ProductInterface $product
     * @return Item
     */
    protected function getProductStockItem(ProductInterface $product): Item
    {
        return $this->stockItem->load($product->getId(), 'product_id');
    }

    /**
     * @param array $products
     * @return ProductInterface
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\InventoryConfigurationApi\Exception\SkuIsNotAssignedToStockException
     */
    protected function chooseMinProduct(array $products): ProductInterface
    {
        $min = null;
        $key = 0;
        foreach ($products as $id => $item) {
            $productQty = $this->getProductQty($item);
            if ($min === null) {
                $min = $productQty;
                $key = $id;
            } elseif ($productQty < $min) {
                $min = $productQty;
                $key = $id;
            }
        }

        return $products[$key];
    }

    /**
     * @param ProductInterface $product
     * @param array $attr
     * @return ProductInterface
     */
    protected function getProductForConfigurableType(ProductInterface $product, array $attr): ProductInterface
    {
        $childCollection = $product->getTypeInstance()->getUsedProductCollection($product);
        $childCollection->setProductFilter($product);
        foreach ($attr as $id => $value) {
            $attr = $product->getTypeInstance()->getAttributeById($id, $product);
            $childCollection->addFieldToFilter($attr->getAttributeCode(), $value);
        }

        return $childCollection->getFirstItem();
    }

}
