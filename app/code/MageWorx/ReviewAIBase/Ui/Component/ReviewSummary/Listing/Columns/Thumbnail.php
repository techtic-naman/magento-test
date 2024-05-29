<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types = 1);

namespace MageWorx\ReviewAIBase\Ui\Component\ReviewSummary\Listing\Columns;

use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Helper\Image as ImageHelper;
use Psr\Log\LoggerInterface;

class Thumbnail extends Column
{
    protected ProductRepositoryInterface $productRepository;
    protected ImageHelper                $imageHelper;
    protected UrlInterface               $urlBuilder;
    protected LoggerInterface            $logger;

    public function __construct(
        ContextInterface           $context,
        UiComponentFactory         $uiComponentFactory,
        ProductRepositoryInterface $productRepository,
        ImageHelper                $imageHelper,
        UrlInterface               $urlBuilder,
        LoggerInterface            $logger,
        array                      $components = [],
        array                      $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->productRepository = $productRepository;
        $this->imageHelper       = $imageHelper;
        $this->urlBuilder        = $urlBuilder;
        $this->logger            = $logger;
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource): array
    {
        if (isset($dataSource['data']['items'])) {
            $fieldName = $this->getData('name');
            foreach ($dataSource['data']['items'] as & $item) {
                $productSku = $item['sku'] ?? '';
                if (empty($productSku)) {
                    continue;
                }

                try {
                    /** @var \Magento\Catalog\Model\Product $product */
                    $product     = $this->productRepository->get($productSku);
                    $imageHelper = $this->imageHelper->init($product, 'product_listing_thumbnail');
                } catch (\Exception $e) {
                    $this->logger->warning($e->getMessage());
                    $this->logger->warning($e->getTraceAsString());
                    continue;
                }

                $item[$fieldName . '_src']      = $imageHelper->getUrl();
                $item[$fieldName . '_alt']      = $this->getAlt($item) ?: $imageHelper->getLabel();
                $origImageHelper                =
                    $this->imageHelper->init($product, 'product_listing_thumbnail_preview');
                $item[$fieldName . '_orig_src'] = $origImageHelper->getUrl();
            }
        }

        return $dataSource;
    }
}
