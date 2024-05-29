<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace MageWorx\ReviewAIBase\Model;

use Magento\Framework\Model\AbstractModel;
use MageWorx\ReviewAIBase\Api\Data\ReviewSummaryInterface;

class ReviewSummary extends AbstractModel implements ReviewSummaryInterface
{
    protected $_idFieldName = 'entity_id';

    protected function _construct()
    {
        $this->_init(\MageWorx\ReviewAIBase\Model\ResourceModel\ReviewSummary::class);
    }

    /**
     * @return int
     */
    public function getEntityId(): int
    {
        return (int)$this->_getData(self::ENTITY_ID);
    }

    /**
     * @param int $entityId
     * @return ReviewSummaryInterface
     */
    public function setEntityId($entityId): ReviewSummaryInterface
    {
        return $this->setData(self::ENTITY_ID, $entityId);
    }

    /**
     * @return int
     */
    public function getProductId(): int
    {
        return (int)$this->_getData(self::PRODUCT_ID);
    }

    /**
     * @param int $productId
     * @return ReviewSummaryInterface
     */
    public function setProductId(int $productId): ReviewSummaryInterface
    {
        return $this->setData(self::PRODUCT_ID, $productId);
    }

    /**
     * @return string|null
     */
    public function getSummaryData(): ?string
    {
        return (string)$this->_getData(self::SUMMARY_DATA) ?? null;
    }

    /**
     * @param string|null $summaryData
     * @return ReviewSummaryInterface
     */
    public function setSummaryData(?string $summaryData): ReviewSummaryInterface
    {
        return $this->setData(self::SUMMARY_DATA, $summaryData);
    }

    /**
     * @return int
     */
    public function getStatus(): int
    {
        return (int)$this->_getData(self::STATUS);
    }

    /**
     * @param int $status
     * @return ReviewSummaryInterface
     */
    public function setStatus(int $status): ReviewSummaryInterface
    {
        return $this->setData(self::STATUS, $status);
    }

    /**
     * @return string
     */
    public function getCreatedAt(): string
    {
        return (string)$this->_getData(self::CREATED_AT);
    }

    /**
     * @param string $createdAt
     * @return ReviewSummaryInterface
     */
    public function setCreatedAt(string $createdAt): ReviewSummaryInterface
    {
        return $this->setData(self::CREATED_AT, $createdAt);
    }

    /**
     * @return string
     */
    public function getUpdatedAt(): string
    {
        return (string)$this->_getData(self::UPDATED_AT);
    }

    /**
     * @param string $updatedAt
     * @return ReviewSummaryInterface
     */
    public function setUpdatedAt(string $updatedAt): ReviewSummaryInterface
    {
        return $this->setData(self::UPDATED_AT, $updatedAt);
    }

    /**
     * @return int|null
     */
    public function getStoreId(): ?int
    {
        return (int)$this->_getData(self::STORE_ID) ?? null;
    }

    /**
     * @param int $storeId
     * @return ReviewSummaryInterface
     */
    public function setStoreId(int $storeId): ReviewSummaryInterface
    {
        return $this->setData(self::STORE_ID, $storeId);
    }

    /**
     * @inheritdoc
     */
    public function getUpdateRequired(): bool
    {
        return (bool)$this->_getData(self::UPDATE_REQUIRED);
    }

    /**
     * @inheritdoc
     */
    public function setUpdateRequired(bool $updateRequired): ReviewSummaryInterface
    {
        return $this->setData(self::UPDATE_REQUIRED, $updateRequired);
    }

    /**
     * @inheritDoc
     */
    public function getIsEnabled(): bool
    {
        return (bool)$this->_getData(self::IS_ENABLED);
    }

    /**
     * @inheritDoc
     */
    public function setIsEnabled(bool $value): ReviewSummaryInterface
    {
        return $this->setData(self::IS_ENABLED, $value);
    }
}
