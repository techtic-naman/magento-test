<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace MageWorx\StockStatus\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\Store;
use MageWorx\StockStatus\Api\Data\StockStatusConfigReaderInterface;

class StockStatusConfigReader implements StockStatusConfigReaderInterface
{
    /**
     * XML config path enable stock status
     */
    const ENABLE_STOCK_STATUS = 'mageworx_marketing_suite/stock_status/enable';

    /**
     * XML config path base path
     */
    const XML_PATH_DISPLAY_STOCK_STATUS_ON = 'mageworx_marketing_suite/stock_status/display_on';

    /**
     * XML config path display in stock message
     */
    const XML_PATH_DISPLAY_IN_STOCK_MESSAGE = 'mageworx_marketing_suite/stock_status/display_in_stock_message';

    /**
     * XML config path for in stock message
     */
    const XML_PATH_IN_STOCK_MESSAGE = 'mageworx_marketing_suite/stock_status/in_stock_message';

    /**
     * XML config path display low stock message
     */
    const XML_PATH_DISPLAY_LOW_STOCK_MESSAGE = 'mageworx_marketing_suite/stock_status/display_low_stock_message';

    /**
     * XML config path low stock message
     */
    const XML_PATH_LOW_STOCK_MESSAGE = 'mageworx_marketing_suite/stock_status/low_stock_message';

    /**
     * XML config path low stock level
     */
    const XML_PATH_LOW_STOCK_LEVEL = 'mageworx_marketing_suite/stock_status/low_stock_level';

    /**
     * XML config path custom low stock value
     */
    const XML_PATH_CUSTOM_LOW_STOCK_VALUE = 'mageworx_marketing_suite/stock_status/custom_low_stock_value';

    /**
     * XML config path display urgent stock message
     */
    const XML_PATH_DISPLAY_URGENT_STOCK_MESSAGE = 'mageworx_marketing_suite/stock_status/display_urgent_stock_message';

    /**
     * XML config path urgent stock message
     */
    const XML_PATH_URGENT_STOCK_MESSAGE = 'mageworx_marketing_suite/stock_status/urgent_stock_message';

    /**
     * XML config path urgent stock value
     */
    const XML_PATH_URGENT_STOCK_VALUE = 'mageworx_marketing_suite/stock_status/urgent_stock_value';

    /**
     * XML config path template
     */
    const XML_PATH_TEMPLATE = 'mageworx_marketing_suite/stock_status/template';

    /**
     * @var int|null
     */
    protected $storeId;

    /**
     * Scope config
     *
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * StockStatusConfigReader constructor.
     *
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(ScopeConfigInterface $scopeConfig)
    {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @return int|null
     */
    public function getStoreId()
    {
        return $this->storeId ?? Store::DEFAULT_STORE_ID;
    }

    /**
     * @param int $storeId
     * @return $this
     */
    public function setStoreId($storeId)
    {
        $this->storeId = $storeId;

        return $this;
    }

    /**
     * @param int $storeId
     * @return bool
     */
    public function isEnabled($storeId = Store::DEFAULT_STORE_ID): bool
    {
        $storeId = $storeId ? $storeId : $this->getStoreId();

        return $this->scopeConfig->isSetFlag(
            self::ENABLE_STOCK_STATUS,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * @param int $storeId
     * @return array
     */
    public function getDisplayOn($storeId = Store::DEFAULT_STORE_ID): array
    {
        $storeId = $storeId ? $storeId : $this->getStoreId();

        return explode(',', $this->scopeConfig->getValue(
            self::XML_PATH_DISPLAY_STOCK_STATUS_ON,
            ScopeInterface::SCOPE_STORE,
            $storeId
        ));
    }

    /**
     * @param int $storeId
     * @return bool
     */
    public function isDisplayInStockMessage($storeId = Store::DEFAULT_STORE_ID): bool
    {
        $storeId = $storeId ? $storeId : $this->getStoreId();

        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_DISPLAY_IN_STOCK_MESSAGE,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * @param int $storeId
     * @return string
     */
    public function getInStockMessage($storeId = Store::DEFAULT_STORE_ID): ?string
    {
        $storeId = $storeId ? $storeId : $this->getStoreId();

        return $this->scopeConfig->getValue(
            self::XML_PATH_IN_STOCK_MESSAGE,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * @param int $storeId
     * @return bool
     */
    public function isDisplayLowStockMessage($storeId = Store::DEFAULT_STORE_ID): bool
    {
        $storeId = $storeId ? $storeId : $this->getStoreId();

        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_DISPLAY_LOW_STOCK_MESSAGE,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * @param int $storeId
     * @return string
     */
    public function getLowStockMessage($storeId = Store::DEFAULT_STORE_ID): ?string
    {
        $storeId = $storeId ? $storeId : $this->getStoreId();

        return $this->scopeConfig->getValue(
            self::XML_PATH_LOW_STOCK_MESSAGE,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * @param int $storeId
     * @return string
     */
    public function getLowStockLevel($storeId = Store::DEFAULT_STORE_ID): ?string
    {
        $storeId = $storeId ? $storeId : $this->getStoreId();

        return $this->scopeConfig->getValue(
            self::XML_PATH_LOW_STOCK_LEVEL,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * @param int $storeId
     * @return string
     */
    public function getLowStockCustomValue($storeId = Store::DEFAULT_STORE_ID): ?int
    {
        $storeId = $storeId ? $storeId : $this->getStoreId();

        return (int)$this->scopeConfig->getValue(
            self::XML_PATH_CUSTOM_LOW_STOCK_VALUE,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * @param int $storeId
     * @return bool
     */
    public function isDisplayUrgentStockMessage($storeId = Store::DEFAULT_STORE_ID): bool
    {
        $storeId = $storeId ? $storeId : $this->getStoreId();

        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_DISPLAY_URGENT_STOCK_MESSAGE,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * @param int $storeId
     * @return string
     */
    public function getUrgentStockMessage($storeId = Store::DEFAULT_STORE_ID): ?string
    {
        $storeId = $storeId ? $storeId : $this->getStoreId();

        return $this->scopeConfig->getValue(
            self::XML_PATH_URGENT_STOCK_MESSAGE,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * @param int $storeId
     * @return string
     */
    public function getUrgentStockValue($storeId = Store::DEFAULT_STORE_ID): ?int
    {
        $storeId = $storeId ? $storeId : $this->getStoreId();

        return (int)$this->scopeConfig->getValue(
            self::XML_PATH_URGENT_STOCK_VALUE,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * @param int $storeId
     * @return bool
     */
    public function getTemplateType($storeId = Store::DEFAULT_STORE_ID): ?string
    {
        $storeId = $storeId ? $storeId : $this->getStoreId();

        return $this->scopeConfig->getValue(
            self::XML_PATH_TEMPLATE,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }
}