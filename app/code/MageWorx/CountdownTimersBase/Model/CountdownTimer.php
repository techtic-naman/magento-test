<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace MageWorx\CountdownTimersBase\Model;

use MageWorx\CountdownTimersBase\Api\Data\CountdownTimerInterface;
use Magento\Framework\Model\AbstractExtensibleModel;
use MageWorx\CountdownTimersBase\Api\Data\CountdownTimerExtensionInterface;

class CountdownTimer extends AbstractExtensibleModel implements CountdownTimerInterface
{
    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix = 'mageworx_countdowntimersbase_countdown_timer';

    /**
     * Event object
     *
     * @var string
     */
    protected $_eventObject = 'countdown_timer';

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\MageWorx\CountdownTimersBase\Model\ResourceModel\CountdownTimer::class);
    }

    /**
     * Get Countdown Timer ID
     *
     * @return string|null
     */
    public function getId(): ?string
    {
        return $this->getData(CountdownTimerInterface::COUNTDOWN_TIMER_ID);
    }

    /**
     * Get Countdown Timer ID
     *
     * @return string|null
     */
    public function getCountdownTimerId(): ?string
    {
        return $this->getData(CountdownTimerInterface::COUNTDOWN_TIMER_ID);
    }

    /**
     * Set Countdown Timer ID
     *
     * @param string $countdownTimerId
     * @return CountdownTimerInterface
     */
    public function setCountdownTimerId($countdownTimerId): CountdownTimerInterface
    {
        return $this->setData(CountdownTimerInterface::COUNTDOWN_TIMER_ID, $countdownTimerId);
    }

    /**
     * Get Name
     *
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->getData(CountdownTimerInterface::NAME);
    }

    /**
     * Set Name
     *
     * @param string $name
     * @return CountdownTimerInterface
     */
    public function setName($name): CountdownTimerInterface
    {
        return $this->setData(CountdownTimerInterface::NAME, $name);
    }

    /**
     * Get Display Mode
     *
     * @return string|null
     */
    public function getDisplayMode(): ?string
    {
        return $this->getData(CountdownTimerInterface::DISPLAY_MODE);
    }

    /**
     * Set Display Mode
     *
     * @param string $displayMode
     * @return CountdownTimerInterface
     */
    public function setDisplayMode($displayMode): CountdownTimerInterface
    {
        return $this->setData(CountdownTimerInterface::DISPLAY_MODE, $displayMode);
    }

    /**
     * Get Status
     *
     * @return string|null
     */
    public function getStatus(): ?string
    {
        return $this->getData(CountdownTimerInterface::STATUS);
    }

    /**
     * Set Status
     *
     * @param string $status
     * @return CountdownTimerInterface
     */
    public function setStatus($status): CountdownTimerInterface
    {
        return $this->setData(CountdownTimerInterface::STATUS, $status);
    }

    /**
     * Get Created At
     *
     * @return string|null
     */
    public function getCreatedAt(): ?string
    {
        return $this->getData(CountdownTimerInterface::CREATED_AT);
    }

    /**
     * Set Created At
     *
     * @param string $createdAt
     *
     * @return CountdownTimerInterface
     */
    public function setCreatedAt($createdAt): CountdownTimerInterface
    {
        return $this->setData(CountdownTimerInterface::CREATED_AT, $createdAt);
    }

    /**
     * Get Updated At
     *
     * @return string|null
     */
    public function getUpdatedAt(): ?string
    {
        return $this->getData(CountdownTimerInterface::UPDATED_AT);
    }

    /**
     * Set Updated At
     *
     * @param string $updatedAt
     *
     * @return CountdownTimerInterface
     */
    public function setUpdatedAt($updatedAt): CountdownTimerInterface
    {
        return $this->setData(CountdownTimerInterface::UPDATED_AT, $updatedAt);
    }

    /**
     * Get Store Ids
     *
     * @return array|null
     */
    public function getStoreIds(): ?array
    {
        return $this->getData(CountdownTimerInterface::STORE_IDS);
    }

    /**
     * Set Store Ids
     *
     * @param array $storeIds
     * @return CountdownTimerInterface
     */
    public function setStoreIds(array $storeIds): CountdownTimerInterface
    {
        return $this->setData(CountdownTimerInterface::STORE_IDS, $storeIds);
    }

    /**
     * Get Customer Group Ids
     *
     * @return array|null
     */
    public function getCustomerGroupIds(): ?array
    {
        return $this->getData(CountdownTimerInterface::CUSTOMER_GROUP_IDS);
    }

    /**
     * Set Customer Group Ids
     *
     * @param array $customerGroupIds
     * @return CountdownTimerInterface
     */
    public function setCustomerGroupIds(array $customerGroupIds): CountdownTimerInterface
    {
        return $this->setData(CountdownTimerInterface::CUSTOMER_GROUP_IDS, $customerGroupIds);
    }

    /**
     * Get Priority
     *
     * @return string|null
     */
    public function getPriority(): ?string
    {
        return $this->getData(CountdownTimerInterface::PRIORITY);
    }

    /**
     * Set Priority
     *
     * @param string $priority
     * @return CountdownTimerInterface
     */
    public function setPriority($priority): CountdownTimerInterface
    {
        return $this->setData(CountdownTimerInterface::PRIORITY, $priority);
    }

    /**
     * Get Theme
     *
     * @return string|null
     */
    public function getTheme(): ?string
    {
        return $this->getData(CountdownTimerInterface::THEME);
    }

    /**
     * Set Theme
     *
     * @param string $theme
     *
     * @return CountdownTimerInterface
     */
    public function setTheme($theme): CountdownTimerInterface
    {
        return $this->setData(CountdownTimerInterface::THEME, $theme);
    }

    /**
     * Get Accent
     *
     * @return string|null
     */
    public function getAccent(): ?string
    {
        return $this->getData(CountdownTimerInterface::ACCENT);
    }

    /**
     * Set Accent
     *
     * @param string $accent
     *
     * @return CountdownTimerInterface
     */
    public function setAccent($accent): CountdownTimerInterface
    {
        return $this->setData(CountdownTimerInterface::ACCENT, $accent);
    }

    /**
     * Get Display On Categories
     *
     * @return int|null
     */
    public function getDisplayOnCategories(): ?int
    {
        return (int)$this->getData(CountdownTimerInterface::DISPLAY_ON_CATEGORIES);
    }

    /**
     * Set Display On Categories
     *
     * @param int $displayOnCategories
     * @return CountdownTimerInterface
     */
    public function setDisplayOnCategories($displayOnCategories): CountdownTimerInterface
    {
        return $this->setData(CountdownTimerInterface::DISPLAY_ON_CATEGORIES, $displayOnCategories);
    }

    /**
     * Get Use Discount Dates
     *
     * @return string|null
     */
    public function getUseDiscountDates(): ?string
    {
        return $this->getData(CountdownTimerInterface::USE_DISCOUNT_DATES);
    }

    /**
     * Set Use Discount Dates
     *
     * @param string $useDiscountDates
     * @return CountdownTimerInterface
     */
    public function setUseDiscountDates($useDiscountDates): CountdownTimerInterface
    {
        return $this->setData(CountdownTimerInterface::USE_DISCOUNT_DATES, $useDiscountDates);
    }

    /**
     * Get Start Date
     *
     * @return string|null
     */
    public function getStartDate(): ?string
    {
        return $this->getData(CountdownTimerInterface::START_DATE);
    }

    /**
     * Set Start Date
     *
     * @param string $startDate
     * @return CountdownTimerInterface
     */
    public function setStartDate($startDate): CountdownTimerInterface
    {
        return $this->setData(CountdownTimerInterface::START_DATE, $startDate);
    }

    /**
     * Get End Date
     *
     * @return string|null
     */
    public function getEndDate(): ?string
    {
        return $this->getData(CountdownTimerInterface::END_DATE);
    }

    /**
     * Set End Date
     *
     * @param string $endDate
     * @return CountdownTimerInterface
     */
    public function setEndDate($endDate): CountdownTimerInterface
    {
        return $this->setData(CountdownTimerInterface::END_DATE, $endDate);
    }

    /**
     * Get Before Timer Text
     *
     * @return string|null
     */
    public function getBeforeTimerText(): ?string
    {
        return $this->getData(CountdownTimerInterface::BEFORE_TIMER_TEXT);
    }

    /**
     * Set Before Timer Text
     *
     * @param string $beforeTimerText
     * @return CountdownTimerInterface
     */
    public function setBeforeTimerText($beforeTimerText): CountdownTimerInterface
    {
        return $this->setData(CountdownTimerInterface::BEFORE_TIMER_TEXT, $beforeTimerText);
    }

    /**
     * Get After Timer Text
     *
     * @return string|null
     */
    public function getAfterTimerText(): ?string
    {
        return $this->getData(CountdownTimerInterface::AFTER_TIMER_TEXT);
    }

    /**
     * Set After Timer Text
     *
     * @param string $afterTimerText
     * @return CountdownTimerInterface
     */
    public function setAfterTimerText($afterTimerText): CountdownTimerInterface
    {
        return $this->setData(CountdownTimerInterface::BEFORE_TIMER_TEXT, $afterTimerText);
    }

    /**
     * Get Products Assign Type
     *
     * @return string|null
     */
    public function getProductsAssignType(): ?string
    {
        return $this->getData(CountdownTimerInterface::PRODUCTS_ASSIGN_TYPE);
    }

    /**
     * Set Products Assign Type
     *
     * @param string $productsAssignType
     * @return CountdownTimerInterface
     */
    public function setProductsAssignType($productsAssignType): CountdownTimerInterface
    {
        return $this->setData(CountdownTimerInterface::PRODUCTS_ASSIGN_TYPE, $productsAssignType);
    }

    /**
     * Get Product Ids
     *
     * @return array|null
     */
    public function getProductIds(): ?array
    {
        return $this->getData(CountdownTimerInterface::PRODUCT_IDS);
    }

    /**
     * Set Product Ids
     *
     * @param array $productIds
     * @return CountdownTimerInterface
     */
    public function setProductIds(array $productIds): CountdownTimerInterface
    {
        return $this->setData(CountdownTimerInterface::PRODUCT_IDS, $productIds);
    }

    /**
     * Get Conditions Serialized
     *
     * @return string|null
     */
    public function getConditionsSerialized(): ?string
    {
        return $this->getData(CountdownTimerInterface::CONDITIONS_SERIALIZED);
    }

    /**
     * Set Conditions Serialized
     *
     * @param string $conditionsSerialized
     *
     * @return CountdownTimerInterface
     */
    public function setConditionsSerialized($conditionsSerialized): CountdownTimerInterface
    {
        return $this->setData(CountdownTimerInterface::CONDITIONS_SERIALIZED, $conditionsSerialized);
    }

    /**
     * @return CountdownTimerExtensionInterface|null
     */
    public function getExtensionAttributes(): ?CountdownTimerExtensionInterface
    {
        return $this->_getExtensionAttributes();
    }

    /**
     * @param CountdownTimerExtensionInterface $extensionAttributes
     * @return CountdownTimerInterface
     */
    public function setExtensionAttributes(
        CountdownTimerExtensionInterface $extensionAttributes
    ): CountdownTimerInterface {
        return $this->_setExtensionAttributes($extensionAttributes);
    }
}
