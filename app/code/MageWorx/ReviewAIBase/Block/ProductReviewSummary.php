<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types = 1);

namespace MageWorx\ReviewAIBase\Block;

use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template;
use Magento\Store\Model\StoreManagerInterface;
use MageWorx\ReviewAIBase\Api\DisplayReviewSummaryValidatorInterface;
use MageWorx\ReviewAIBase\Helper\Config;
use MageWorx\ReviewAIBase\Model\ReviewSummaryLoader;

class ProductReviewSummary extends Template
{
    protected ReviewSummaryLoader   $reviewSummaryLoader;
    protected Registry              $registry;
    protected StoreManagerInterface $storeManager;
    protected Config                $config;

    protected DisplayReviewSummaryValidatorInterface $displayReviewSummaryValidator;

    /**
     * Unique cache tag
     */
    const CACHE_TAG = 'mageworx_reviewai_product_review_summary';

    /**
     * @var array
     */
    protected array $_cacheTags = [self::CACHE_TAG];

    public function __construct(
        ReviewSummaryLoader                    $reviewSummaryLoader,
        Registry                               $registry,
        StoreManagerInterface                  $storeManager,
        DisplayReviewSummaryValidatorInterface $displayReviewSummaryValidator,
        Template\Context                       $context,
        Config                                 $config,
        array                                  $data = []
    ) {
        $this->reviewSummaryLoader           = $reviewSummaryLoader;
        $this->registry                      = $registry;
        $this->storeManager                  = $storeManager;
        $this->displayReviewSummaryValidator = $displayReviewSummaryValidator;
        $this->config                        = $config;
        parent::__construct($context, $data);
    }

    /**
     * Get current product id
     *
     * @return int
     */
    public function getProductId(): int
    {
        $product = $this->registry->registry('current_product');

        if ($product) {
            return (int)$product->getId();
        }

        return 0;
    }

    /**
     * Get current store id
     *
     * @return int
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getStoreId(): int
    {
        return (int)$this->storeManager->getStore()->getId();
    }

    /**
     * Get summary review by current product and store id.
     * Empty string returned by this method means there was no review summary for this product on this store.
     *
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getSummaryReview(): string
    {
        if (!$this->config->isEnabled()) {
            return '';
        }

        $productId = $this->getProductId();
        $storeId   = $this->getStoreId();

        if ($productId && $storeId) {
            $rs = $this->reviewSummaryLoader->getByProductIdAndStoreId($productId, $storeId);

            return $rs->getSummaryData();
        }

        return '';
    }

    /**
     * Retrieve unique cache tag
     *
     * @return string[]
     */
    protected function getCacheTags(): array
    {
        $additionalCacheTags = [];
        if ($this->getProductId()) {
            $additionalCacheTags[] = 'mw_rs_' . $this->getProductId();
        }

        return array_unique(array_merge(parent::getCacheTags(), $this->_cacheTags, $additionalCacheTags));
    }

    /**
     * @return string
     */
    public function getReviewSummaryTitle(): string
    {
        return $this->_escaper->escapeHtml($this->config->getTitle());
    }

    /**
     * @return string
     */
    public function getReviewSummaryDescription(): string
    {
        return $this->_escaper->escapeHtml($this->config->getDescription(), ['a', 'b', 'br', 'em', 'i', 'strong']);
    }

    /**
     * Check is need to display the review summary block on product page
     *
     * @return bool
     */
    public function isNeedToDisplayReviewSummary(): bool
    {
        $productId = $this->getProductId();
        if (!$productId) {
            return false;
        }

        try {
            return $this->displayReviewSummaryValidator->validate($productId);
        } catch (\Exception $exception) {
            $this->_logger->error($exception);

            return false;
        }
    }

    /**
     * Returns the summary styles as a string.
     *
     * The summary styles include color, background color, and font size.
     * If any of these styles are set in the configuration, they will be included in
     * the returned string in the format "attribute: value; attribute: value; attribute: value".
     *
     * @return string The summary styles as a formatted string.
     */
    public function getSummaryStyles(): string
    {
        $styleAttributes = [];
        $textColor       = $this->config->getReviewSummaryTextColor();
        if ($textColor) {
            $styleAttributes['color'] = $textColor;
        }

        $bgColor = $this->config->getReviewSummaryBgColor();
        if ($bgColor) {
            $styleAttributes['background-color'] = $bgColor;
        }

        $textSize = $this->config->getReviewSummaryTextSize();
        if ($textSize) {
            $styleAttributes['font-size'] = $textSize . 'px';
        }

        foreach ($styleAttributes as $attribute => $value) {
            $styleAttributes[$attribute] = $attribute . ': ' . $value;
        }

        return $this->_escaper->escapeHtmlAttr(implode('; ', $styleAttributes));
    }

    /**
     * Retrieves the styles for the header based on the configuration.
     *
     * @return string The CSS styles for the header.
     */
    public function getHeaderStyles(): string
    {
        $styleAttributes = [];

        $textColor = $this->config->getReviewSummaryHeaderColor();
        if ($textColor) {
            $styleAttributes['color'] = $textColor;
        }

        $bgColor = $this->config->getReviewSummaryHeaderBgColor();
        if ($bgColor) {
            $styleAttributes['background-color'] = $bgColor;
        }

        $textSize = $this->config->getReviewSummaryHeaderSize();
        if ($textSize) {
            $styleAttributes['font-size'] = $textSize . 'px';
        }

        foreach ($styleAttributes as $attribute => $value) {
            $styleAttributes[$attribute] = $attribute . ': ' . $value;
        }

        return $this->_escaper->escapeHtmlAttr(implode('; ', $styleAttributes));
    }

    /**
     * Returns the styles for the main container.
     *
     * @return string The styles for the main container in CSS format.
     */
    public function getMainContainerStyles(): string
    {
        $styleAttributes = [];

        $bgColor = $this->config->getReviewSummaryMainContainerBgColor();
        if ($bgColor) {
            $styleAttributes['background-color'] = $bgColor;
        }

        foreach ($styleAttributes as $attribute => $value) {
            $styleAttributes[$attribute] = $attribute . ': ' . $value;
        }

        return $this->_escaper->escapeHtmlAttr(implode('; ', $styleAttributes));
    }
}

