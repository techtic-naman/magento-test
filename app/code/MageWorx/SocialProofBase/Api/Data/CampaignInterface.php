<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace MageWorx\SocialProofBase\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

/**
 * @api
 */
interface CampaignInterface extends ExtensibleDataInterface
{
    const CAMPAIGN_ID                               = 'campaign_id';
    const NAME                                      = 'name';
    const DISPLAY_MODE                              = 'display_mode';
    const EVENT_TYPE                                = 'event_type';
    const STATUS                                    = 'status';
    const DISPLAY_ON_MOBILE                         = 'display_on_mobile';
    const CREATED_AT                                = 'created_at';
    const UPDATED_AT                                = 'updated_at';
    const STORE_IDS                                 = 'store_ids';
    const CUSTOMER_GROUP_IDS                        = 'customer_group_ids';
    const RESTRICT_TO_CURRENT_PRODUCT               = 'restrict_to_current_product';
    const PERIOD                                    = 'period';
    const START_DELAY                               = 'start_delay';
    const AUTO_CLOSE_IN                             = 'auto_close_in';
    const MIN_NUMBER_OF_VIEWS                       = 'min_number_of_views';
    const MAX_NUMBER_PER_PAGE                       = 'max_number_per_page';
    const PRIORITY                                  = 'priority';
    const DISPLAY_ON                                = 'display_on';
    const REMOVE_VERIFIED                           = 'remove_verified';
    const PRODUCT_IDS                               = 'product_ids';
    const CATEGORY_IDS                              = 'category_ids';
    const CMS_PAGE_IDS                              = 'cms_page_ids';
    const POSITION                                  = 'position';
    const CONTENT                                   = 'content';
    const PRODUCTS_ASSIGN_TYPE                      = 'products_assign_type';
    const DISPLAY_ON_PRODUCTS_CONDITIONS_SERIALIZED = 'display_on_products_conditions_serialized';
    const CATEGORIES_ASSIGN_TYPE                    = 'categories_assign_type';
    const CMS_PAGES_ASSIGN_TYPE                     = 'cms_pages_assign_type';
    const RESTRICTION_CONDITIONS_SERIALIZED         = 'restriction_conditions_serialized';

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
    public function getCampaignId(): ?string;

    /**
     * Set ID
     *
     * @param string $campaignId
     * @return CampaignInterface
     */
    public function setCampaignId($campaignId): CampaignInterface;

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
     * @return CampaignInterface
     */
    public function setName($name): CampaignInterface;

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
     * @return CampaignInterface
     */
    public function setDisplayMode($displayMode): CampaignInterface;

    /**
     * Get Event Type
     *
     * @return string|null
     */
    public function getEventType(): ?string;

    /**
     * Set Event Type
     *
     * @param string $eventType
     * @return CampaignInterface
     */
    public function setEventType($eventType): CampaignInterface;

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
     * @return CampaignInterface
     */
    public function setStatus($status): CampaignInterface;

    /**
     * Get Display On Mobile
     *
     * @return string|null
     */
    public function getDisplayOnMobile(): ?string;

    /**
     * Set Display On Mobile
     *
     * @param string $displayOnMobile
     * @return CampaignInterface
     */
    public function setDisplayOnMobile($displayOnMobile): CampaignInterface;

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
     * @return CampaignInterface
     */
    public function setCreatedAt($createdAt): CampaignInterface;

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
     * @return CampaignInterface
     */
    public function setUpdatedAt($updatedAt): CampaignInterface;

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
     * @return CampaignInterface
     */
    public function setStoreIds($storeIds): CampaignInterface;

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
     * @return CampaignInterface
     */
    public function setCustomerGroupIds($customerGroupIds): CampaignInterface;

    /**
     * Get Restrict To Current Product
     *
     * @return string|null
     */
    public function getRestrictToCurrentProduct(): ?string;

    /**
     * Set Restrict To Current Product
     *
     * @param string $restrictToCurrentProduct
     * @return CampaignInterface
     */
    public function setRestrictToCurrentProduct($restrictToCurrentProduct): CampaignInterface;

    /**
     * Get Period
     *
     * @return string|null
     */
    public function getPeriod(): ?string;

    /**
     * Set Period
     *
     * @param string $period
     * @return CampaignInterface
     */
    public function setPeriod($period): CampaignInterface;

    /**
     * Get Start Delay
     *
     * @return string|null
     */
    public function getStartDelay(): ?string;

    /**
     * Set Start Delay
     *
     * @param string $startDelay
     * @return CampaignInterface
     */
    public function setStartDelay($startDelay): CampaignInterface;

    /**
     * Get Auto Close In
     *
     * @return string|null
     */
    public function getAutoCloseIn(): ?string;

    /**
     * Set Auto Close In
     *
     * @param string $autoCloseIn
     * @return CampaignInterface
     */
    public function setAutoCloseIn($autoCloseIn): CampaignInterface;

    /**
     * Get Min Number Of Views
     *
     * @return string|null
     */
    public function getMinNumberOfViews(): ?string;

    /**
     * Set Min Number Of Views
     *
     * @param string $minNumberOfViews
     * @return CampaignInterface
     */
    public function setMinNumberOfViews($minNumberOfViews): CampaignInterface;

    /**
     * Get Max Number Per Page
     *
     * @return string|null
     */
    public function getMaxNumberPerPage(): ?string;

    /**
     * Set Max Number Per Page
     *
     * @param string $maxNumberPerPage
     * @return CampaignInterface
     */
    public function setMaxNumberPerPage($maxNumberPerPage): CampaignInterface;

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
     * @return CampaignInterface
     */
    public function setPriority($priority): CampaignInterface;

    /**
     * Get Remove Verified
     *
     * @return string|null
     */
    public function getRemoveVerified(): ?string;

    /**
     * Set Remove Verified
     *
     * @param string $removeVerified
     * @return CampaignInterface
     */
    public function setRemoveVerified($removeVerified): CampaignInterface;

    /**
     * Get Display On
     *
     * @return array|null
     */
    public function getDisplayOn(): ?array;

    /**
     * Set Display On
     *
     * @param array $displayOn
     * @return CampaignInterface
     */
    public function setDisplayOn($displayOn): CampaignInterface;

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
     * @return CampaignInterface
     */
    public function setProductIds($productIds): CampaignInterface;

    /**
     * Get Category Ids
     *
     * @return array|null
     */
    public function getCategoryIds(): ?array;

    /**
     * Set Category Ids
     *
     * @param array $categoryIds
     * @return CampaignInterface
     */
    public function setCategoryIds($categoryIds): CampaignInterface;

    /**
     * Get CMS Page Ids
     *
     * @return array|null
     */
    public function getCmsPageIds(): ?array;

    /**
     * Set CMS Page Ids
     *
     * @param array $cmsPageIds
     * @return CampaignInterface
     */
    public function setCmsPageIds($cmsPageIds): CampaignInterface;

    /**
     * Get Position
     *
     * @return string|null
     */
    public function getPosition(): ?string;

    /**
     * Set Position
     *
     * @param string $position
     *
     * @return CampaignInterface
     */
    public function setPosition($position): CampaignInterface;

    /**
     * Get Content
     *
     * @return string|null
     */
    public function getContent(): ?string;

    /**
     * Set Content
     *
     * @param string $content
     *
     * @return CampaignInterface
     */
    public function setContent($content): CampaignInterface;

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
     * @return CampaignInterface
     */
    public function setProductsAssignType($productsAssignType): CampaignInterface;

    /**
     * Get Display On Products Conditions Serialized
     *
     * @return string|null
     */
    public function getDisplayOnProductsConditionsSerialized(): ?string;

    /**
     * Set Display On Products Conditions Serialized
     *
     * @param string $conditionsSerialized
     *
     * @return CampaignInterface
     */
    public function setDisplayOnProductsConditionsSerialized($conditionsSerialized): CampaignInterface;

    /**
     * Get Categories Assign Type
     *
     * @return string|null
     */
    public function getCategoriesAssignType(): ?string;

    /**
     * Set Categories Assign Type
     *
     * @param string $categoriesAssignType
     * @return CampaignInterface
     */
    public function setCategoriesAssignType($categoriesAssignType): CampaignInterface;

    /**
     * Get CMS Pages Assign Type
     *
     * @return string|null
     */
    public function getCmsPagesAssignType(): ?string;

    /**
     * Set CMS Pages Assign Type
     *
     * @param string $cmsPagesAssignType
     * @return CampaignInterface
     */
    public function setCmsPagesAssignType($cmsPagesAssignType): CampaignInterface;

    /**
     * Get Restriction Conditions Serialized
     *
     * @return string|null
     */
    public function getRestrictionConditionsSerialized(): ?string;

    /**
     * Set Restriction Conditions Serialized
     *
     * @param string $conditionsSerialized
     *
     * @return CampaignInterface
     */
    public function setRestrictionConditionsSerialized($conditionsSerialized): CampaignInterface;

    /**
     * @return \MageWorx\SocialProofBase\Api\Data\CampaignExtensionInterface|null
     */
    public function getExtensionAttributes(): ?\MageWorx\SocialProofBase\Api\Data\CampaignExtensionInterface;

    /**
     * @param \MageWorx\SocialProofBase\Api\Data\CampaignExtensionInterface $extensionAttributes
     * @return CampaignInterface
     */
    public function setExtensionAttributes(
        \MageWorx\SocialProofBase\Api\Data\CampaignExtensionInterface $extensionAttributes
    ): CampaignInterface;
}
