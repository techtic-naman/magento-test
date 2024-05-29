<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types = 1);

namespace MageWorx\ReviewAIBase\Api\Data;

interface ReviewSummaryInterface
{
    const ENTITY_ID       = 'entity_id';
    const PRODUCT_ID      = 'product_id';
    const SUMMARY_DATA    = 'summary_data';
    const STATUS          = 'status';
    const CREATED_AT      = 'created_at';
    const UPDATED_AT      = 'updated_at';
    const STORE_ID        = 'store_id';
    const UPDATE_REQUIRED = 'update_required';
    const IS_ENABLED         = 'is_enabled';

    /**
     * @return int
     */
    public function getEntityId(): int;

    /**
     * @param int $entityId
     * @return $this
     */
    public function setEntityId(int $entityId): ReviewSummaryInterface;

    /**
     * @return int
     */
    public function getProductId(): int;

    /**
     * @param int $productId
     * @return $this
     */
    public function setProductId(int $productId): ReviewSummaryInterface;

    /**
     * @return string|null
     */
    public function getSummaryData(): ?string;

    /**
     * @param string|null $summaryData
     * @return $this
     */
    public function setSummaryData(?string $summaryData): ReviewSummaryInterface;

    /**
     * @return int
     */
    public function getStatus(): int;

    /**
     * @param int $status
     * @return $this
     */
    public function setStatus(int $status): ReviewSummaryInterface;

    /**
     * @return string
     */
    public function getCreatedAt(): string;

    /**
     * @param string $createdAt
     * @return $this
     */
    public function setCreatedAt(string $createdAt): ReviewSummaryInterface;

    /**
     * @return string
     */
    public function getUpdatedAt(): string;

    /**
     * @param string $updatedAt
     * @return $this
     */
    public function setUpdatedAt(string $updatedAt): ReviewSummaryInterface;

    /**
     * @return int|null
     */
    public function getStoreId(): ?int;

    /**
     * @param int $storeId
     * @return $this
     */
    public function setStoreId(int $storeId): ReviewSummaryInterface;

    /**
     * @return bool
     */
    public function getUpdateRequired(): bool;

    /**
     * @param bool $updateRequired
     * @return $this
     */
    public function setUpdateRequired(bool $updateRequired): ReviewSummaryInterface;

    /**
     * @return bool
     */
    public function getIsEnabled(): bool;

    /**
     * @param bool $value
     * @return ReviewSummaryInterface
     */
    public function setIsEnabled(bool $value): ReviewSummaryInterface;
}
