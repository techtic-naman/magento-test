<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace MageWorx\CountdownTimersBase\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

/**
 * @api
 */
interface CountdownTimerInterface extends ExtensibleDataInterface
{
    const COUNTDOWN_TIMER_ID    = 'countdown_timer_id';
    const NAME                  = 'name';
    const DISPLAY_MODE          = 'display_mode';
    const STATUS                = 'status';
    const CREATED_AT            = 'created_at';
    const UPDATED_AT            = 'updated_at';
    const STORE_IDS             = 'store_ids';
    const CUSTOMER_GROUP_IDS    = 'customer_group_ids';
    const PRIORITY              = 'priority';
    const THEME                 = 'theme';
    const ACCENT                = 'accent';
    const DISPLAY_ON_CATEGORIES = 'display_on_categories';
    const USE_DISCOUNT_DATES    = 'use_discount_dates';
    const START_DATE            = 'start_date';
    const END_DATE              = 'end_date';
    const BEFORE_TIMER_TEXT     = 'before_timer_text';
    const AFTER_TIMER_TEXT      = 'after_timer_text';
    const PRODUCTS_ASSIGN_TYPE  = 'products_assign_type';
    const PRODUCT_IDS           = 'product_ids';
    const CONDITIONS_SERIALIZED = 'conditions_serialized';

    /**
     * Get ID
     *
     * @return string|null
     */
    public function getId(): ?string;

    /**
     * Get ID
     *
     * @return string|null
     */
    public function getCountdownTimerId(): ?string;

    /**
     * Set ID
     *
     * @param string $countdownTimerId
     * @return CountdownTimerInterface
     */
    public function setCountdownTimerId($countdownTimerId): CountdownTimerInterface;

    /**
     * Get Name
     *
     * @return string|null
     */
    public function getName(): ?string;

    /**
     * Set Name
     *
     * @param string $name
     * @return CountdownTimerInterface
     */
    public function setName($name): CountdownTimerInterface;

    /**
     * Get Display Mode
     *
     * @return string|null
     */
    public function getDisplayMode(): ?string;

    /**
     * Set Display Mode
     *
     * @param string $displayMode
     * @return CountdownTimerInterface
     */
    public function setDisplayMode($displayMode): CountdownTimerInterface;

    /**
     * Get Status
     *
     * @return string|null
     */
    public function getStatus(): ?string;

    /**
     * Set Status
     *
     * @param string $status
     * @return CountdownTimerInterface
     */
    public function setStatus($status): CountdownTimerInterface;

    /**
     * Get Created At
     *
     * @return string|null
     */
    public function getCreatedAt(): ?string;

    /**
     * Set Created At
     *
     * @param string $createdAt
     *
     * @return CountdownTimerInterface
     */
    public function setCreatedAt($createdAt): CountdownTimerInterface;

    /**
     * Get Updated at
     *
     * @return string|null
     */
    public function getUpdatedAt(): ?string;

    /**
     * Set Updated At
     *
     * @param string $updatedAt
     *
     * @return CountdownTimerInterface
     */
    public function setUpdatedAt($updatedAt): CountdownTimerInterface;

    /**
     * Get Store Ids
     *
     * @return array|null
     */
    public function getStoreIds(): ?array;

    /**
     * Set Store Ids
     *
     * @param array $storeIds
     * @return CountdownTimerInterface
     */
    public function setStoreIds(array $storeIds): CountdownTimerInterface;

    /**
     * Get Customer Group Ids
     *
     * @return array|null
     */
    public function getCustomerGroupIds(): ?array;

    /**
     * Set Customer Group Ids
     *
     * @param array $customerGroupIds
     * @return CountdownTimerInterface
     */
    public function setCustomerGroupIds(array $customerGroupIds): CountdownTimerInterface;

    /**
     * Get Priority
     *
     * @return string|null
     */
    public function getPriority(): ?string;

    /**
     * Set Priority
     *
     * @param string $priority
     * @return CountdownTimerInterface
     */
    public function setPriority($priority): CountdownTimerInterface;

    /**
     * Get Theme
     *
     * @return string|null
     */
    public function getTheme(): ?string;

    /**
     * Set Theme
     *
     * @param string $theme
     *
     * @return CountdownTimerInterface
     */
    public function setTheme($theme): CountdownTimerInterface;

    /**
     * Get Accent
     *
     * @return string|null
     */
    public function getAccent(): ?string;

    /**
     * Set Accent
     *
     * @param string $accent
     *
     * @return CountdownTimerInterface
     */
    public function setAccent($accent): CountdownTimerInterface;

    /**
     * Get Display On Categories
     *
     * @return int|null
     */
    public function getDisplayOnCategories(): ?int;

    /**
     * Set Display On Categories
     *
     * @param int $displayOnCategories
     * @return CountdownTimerInterface
     */
    public function setDisplayOnCategories($displayOnCategories): CountdownTimerInterface;

    /**
     * Get Use Discount Dates
     *
     * @return string|null
     */
    public function getUseDiscountDates(): ?string;

    /**
     * Set Use Discount Dates
     *
     * @param string $useDiscountDates
     * @return CountdownTimerInterface
     */
    public function setUseDiscountDates($useDiscountDates): CountdownTimerInterface;

    /**
     * Get Start Date
     *
     * @return string|null
     */
    public function getStartDate(): ?string;

    /**
     * Set Start Date
     *
     * @param string $startDate
     * @return CountdownTimerInterface
     */
    public function setStartDate($startDate): CountdownTimerInterface;

    /**
     * Get End Date
     *
     * @return string|null
     */
    public function getEndDate(): ?string;

    /**
     * Set End Date
     *
     * @param string $endDate
     * @return CountdownTimerInterface
     */
    public function setEndDate($endDate): CountdownTimerInterface;

    /**
     * Get Before Timer Text
     *
     * @return string|null
     */
    public function getBeforeTimerText(): ?string;

    /**
     * Set Before Timer Text
     *
     * @param string $beforeTimerText
     * @return CountdownTimerInterface
     */
    public function setBeforeTimerText($beforeTimerText): CountdownTimerInterface;

    /**
     * Get After Timer Text
     *
     * @return string|null
     */
    public function getAfterTimerText(): ?string;

    /**
     * Set After Timer Text
     *
     * @param string $afterTimerText
     * @return CountdownTimerInterface
     */
    public function setAfterTimerText($afterTimerText): CountdownTimerInterface;

    /**
     * Get Products Assign Type
     *
     * @return string|null
     */
    public function getProductsAssignType(): ?string;

    /**
     * Set Products Assign Type
     *
     * @param string $productsAssignType
     * @return CountdownTimerInterface
     */
    public function setProductsAssignType($productsAssignType): CountdownTimerInterface;

    /**
     * Get Product Ids
     *
     * @return array|null
     */
    public function getProductIds(): ?array;

    /**
     * Set Product Ids
     *
     * @param array $productIds
     * @return CountdownTimerInterface
     */
    public function setProductIds(array $productIds): CountdownTimerInterface;

    /**
     * Get Conditions Serialized
     *
     * @return string|null
     */
    public function getConditionsSerialized(): ?string;

    /**
     * Set Conditions Serialized
     *
     * @param string $conditionsSerialized
     *
     * @return CountdownTimerInterface
     */
    public function setConditionsSerialized($conditionsSerialized): CountdownTimerInterface;

    /**
     * @return \MageWorx\CountdownTimersBase\Api\Data\CountdownTimerExtensionInterface|null
     */
    public function getExtensionAttributes(): ?\MageWorx\CountdownTimersBase\Api\Data\CountdownTimerExtensionInterface;

    /**
     * @param \MageWorx\CountdownTimersBase\Api\Data\CountdownTimerExtensionInterface $extensionAttributes
     * @return CountdownTimerInterface
     */
    public function setExtensionAttributes(
        \MageWorx\CountdownTimersBase\Api\Data\CountdownTimerExtensionInterface $extensionAttributes
    ): CountdownTimerInterface;
}
