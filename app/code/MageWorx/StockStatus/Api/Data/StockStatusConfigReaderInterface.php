<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace MageWorx\StockStatus\Api\Data;

use Magento\Store\Model\Store;

/**
 * Stock Status config reader interface
 */
interface StockStatusConfigReaderInterface
{
    /**
     * @return int|null
     */
    public function getStoreId();

    /**
     * @param int $storeId
     * @return $this
     */
    public function setStoreId($storeId);

    /**
     * @param int $storeId
     * @return bool
     */
    public function isEnabled($storeId = Store::DEFAULT_STORE_ID): bool;

    /**
     * @param int $storeId
     * @return array
     */
    public function getDisplayOn($storeId = Store::DEFAULT_STORE_ID): array;

    /**
     * @param int $storeId
     * @return bool
     */
    public function isDisplayInStockMessage($storeId = Store::DEFAULT_STORE_ID): bool;

    /**
     * @param int $storeId
     * @return string
     */
    public function getInStockMessage($storeId = Store::DEFAULT_STORE_ID): ?string;

    /**
     * @param int $storeId
     * @return bool
     */
    public function isDisplayLowStockMessage($storeId = Store::DEFAULT_STORE_ID): bool;

    /**
     * @param int $storeId
     * @return string
     */
    public function getLowStockMessage($storeId = Store::DEFAULT_STORE_ID): ?string;

    /**
     * @param int $storeId
     * @return string
     */
    public function getLowStockLevel($storeId = Store::DEFAULT_STORE_ID): ?string;

    /**
     * @param int $storeId
     * @return string
     */
    public function getLowStockCustomValue($storeId = Store::DEFAULT_STORE_ID): ?int;

    /**
     * @param int $storeId
     * @return bool
     */
    public function isDisplayUrgentStockMessage($storeId = Store::DEFAULT_STORE_ID): bool;

    /**
     * @param int $storeId
     * @return string
     */
    public function getUrgentStockMessage($storeId = Store::DEFAULT_STORE_ID): ?string;

    /**
     * @param int $storeId
     * @return string
     */
    public function getUrgentStockValue($storeId = Store::DEFAULT_STORE_ID): ?int;

    /**
     * @param int $storeId
     * @return bool
     */
    public function getTemplateType($storeId = Store::DEFAULT_STORE_ID): ?string;
}