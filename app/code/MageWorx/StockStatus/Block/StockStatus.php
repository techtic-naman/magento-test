<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace MageWorx\StockStatus\Block;

use MageWorx\StockStatus\Model\Source\DisplayOn;
use MageWorx\StockStatus\Model\Source\Template;
use Magento\Catalog\Api\Data\ProductInterface;
use MageWorx\StockStatus\Model\ProductStock;
use MageWorx\StockStatus\Api\Data\StockStatusConfigReaderInterface;

/**
 * Class StockStatus
 */
class StockStatus extends \Magento\Framework\View\Element\Template
{

    const CACHE_TAG = 'mageworx-brands';

    /**
     * @var  StockStatusConfigReaderInterface
     */
    protected $stockStatusConfigReader;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var ProductStock
     */
    protected $productStock;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry;

    /**
     * @var \Magento\Framework\App\Request\Http
     */
    protected $httpRequest;

    /**
     * StockStatus constructor.
     *
     * @param \Magento\Framework\App\Request\Http $httpRequest
     * @param \Magento\Framework\Registry $coreRegistry
     * @param ProductStock $productStock
     * @param StockStatusConfigReaderInterface $stockStatusConfigReader
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\App\Request\Http $httpRequest,
        \Magento\Framework\Registry $coreRegistry,
        ProductStock $productStock,
        StockStatusConfigReaderInterface $stockStatusConfigReader,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\View\Element\Template\Context $context,
        array $data = []
    ) {
        $this->httpRequest             = $httpRequest;
        $this->coreRegistry            = $coreRegistry;
        $this->productStock            = $productStock;
        $this->storeManager            = $storeManager;
        $this->stockStatusConfigReader = $stockStatusConfigReader;
        parent::__construct($context, $data);
    }

    /**
     * @return \Magento\Framework\View\Element\AbstractBlock
     */
    protected function _prepareLayout(): \Magento\Framework\View\Element\AbstractBlock
    {
        $this->stockStatusConfigReader->setStoreId($this->storeManager->getStore()->getId());

        if ($this->httpRequest->getFullActionName() === 'catalog_product_view' &&
            array_search(DisplayOn::PRODUCT_PAGE, $this->stockStatusConfigReader->getDisplayOn()) === false
        ) {
            return parent::_prepareLayout();
        }

        switch ($this->stockStatusConfigReader->getTemplateType()) {
            case Template::DEFAULT:
            default:
                $this->setTemplate('MageWorx_StockStatus::default.phtml');
                break;
        }

        return parent::_prepareLayout();
    }

    /**
     * @return string
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\InventoryConfigurationApi\Exception\SkuIsNotAssignedToStockException
     */
    public function getStockText(): string
    {
        /** @var ProductInterface $product */
        $product = $this->getProduct();

        if (!$product) {
            $product = $this->coreRegistry->registry('current_product');
        }

        if (!$product) {
            return '';
        }

        return $this->productStock->getProductStockText($product);
    }

    /**
     * @return int|null
     */
    public function getProductId(): string
    {
        /** @var ProductInterface $product */
        $product = $this->getProduct();

        if (!$product) {
            $product = $this->coreRegistry->registry('current_product');
        }

        if (!$product) {
            return '';
        }

        return (string)$product->getId();
    }
}