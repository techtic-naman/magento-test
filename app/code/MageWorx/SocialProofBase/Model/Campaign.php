<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace MageWorx\SocialProofBase\Model;

use MageWorx\SocialProofBase\Api\Data\CampaignInterface;
use Magento\Framework\Model\AbstractExtensibleModel;
use MageWorx\SocialProofBase\Api\Data\CampaignExtensionInterface;

class Campaign extends AbstractExtensibleModel implements CampaignInterface
{
    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix = 'mageworx_socialproofbase_campaign';

    /**
     * Event object
     *
     * @var string
     */
    protected $_eventObject = 'campaign';

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\MageWorx\SocialProofBase\Model\ResourceModel\Campaign::class);
    }

    /**
     * Get Campaign id
     *
     * @return string|null
     */
    public function getId(): ?string
    {
        return $this->getData(CampaignInterface::CAMPAIGN_ID);
    }

    /**
     * Get Campaign id
     *
     * @return string|null
     */
    public function getCampaignId(): ?string
    {
        return $this->getData(CampaignInterface::CAMPAIGN_ID);
    }

    /**
     * Set Campaign id
     *
     * @param string $campaignId
     * @return CampaignInterface
     */
    public function setCampaignId($campaignId): CampaignInterface
    {
        return $this->setData(CampaignInterface::CAMPAIGN_ID, $campaignId);
    }

    /**
     * Set Name
     *
     * @param string $name
     * @return CampaignInterface
     */
    public function setName($name): CampaignInterface
    {
        return $this->setData(CampaignInterface::NAME, $name);
    }

    /**
     * Get Name
     *
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->getData(CampaignInterface::NAME);
    }

    /**
     * Set Display Mode
     *
     * @param string $displayMode
     * @return CampaignInterface
     */
    public function setDisplayMode($displayMode): CampaignInterface
    {
        return $this->setData(CampaignInterface::DISPLAY_MODE, $displayMode);
    }

    /**
     * Get Display Mode
     *
     * @return string|null
     */
    public function getDisplayMode(): ?string
    {
        return $this->getData(CampaignInterface::DISPLAY_MODE);
    }

    /**
     * Set Event Type
     *
     * @param string $eventType
     * @return CampaignInterface
     */
    public function setEventType($eventType): CampaignInterface
    {
        return $this->setData(CampaignInterface::EVENT_TYPE, $eventType);
    }

    /**
     * Get Event Type
     *
     * @return string|null
     */
    public function getEventType(): ?string
    {
        return $this->getData(CampaignInterface::EVENT_TYPE);
    }

    /**
     * Set Status
     *
     * @param string $status
     * @return CampaignInterface
     */
    public function setStatus($status): CampaignInterface
    {
        return $this->setData(CampaignInterface::STATUS, $status);
    }

    /**
     * Get Status
     *
     * @return string|null
     */
    public function getStatus(): ?string
    {
        return $this->getData(CampaignInterface::STATUS);
    }

    /**
     * Set Display On Mobile
     *
     * @param string $displayOnMobile
     * @return CampaignInterface
     */
    public function setDisplayOnMobile($displayOnMobile): CampaignInterface
    {
        return $this->setData(CampaignInterface::DISPLAY_ON_MOBILE, $displayOnMobile);
    }

    /**
     * Get Display On Mobile
     *
     * @return string|null
     */
    public function getDisplayOnMobile(): ?string
    {
        return $this->getData(CampaignInterface::DISPLAY_ON_MOBILE);
    }

    /**
     * Set Created At
     *
     * @param string $createdAt
     *
     * @return CampaignInterface
     */
    public function setCreatedAt($createdAt): CampaignInterface
    {
        return $this->setData(CampaignInterface::CREATED_AT, $createdAt);
    }

    /**
     * Get Created At
     *
     * @return string|null
     */
    public function getCreatedAt(): ?string
    {
        return $this->getData(CampaignInterface::CREATED_AT);
    }

    /**
     * Set Updated At
     *
     * @param string $updatedAt
     *
     * @return CampaignInterface
     */
    public function setUpdatedAt($updatedAt): CampaignInterface
    {
        return $this->setData(CampaignInterface::UPDATED_AT, $updatedAt);
    }

    /**
     * Get Updated At
     *
     * @return string|null
     */
    public function getUpdatedAt(): ?string
    {
        return $this->getData(CampaignInterface::UPDATED_AT);
    }

    /**
     * Get Store Ids
     *
     * @return array|null
     */
    public function getStoreIds(): ?array
    {
        return $this->getData(CampaignInterface::STORE_IDS);
    }

    /**
     * Set Store Ids
     *
     * @param array $storeIds
     * @return CampaignInterface
     */
    public function setStoreIds($storeIds): CampaignInterface
    {
        return $this->setData(CampaignInterface::STORE_IDS, $storeIds);
    }

    /**
     * Get Customer Group Ids
     *
     * @return array|null
     */
    public function getCustomerGroupIds(): ?array
    {
        return $this->getData(CampaignInterface::CUSTOMER_GROUP_IDS);
    }

    /**
     * Set Customer Group Ids
     *
     * @param array $customerGroupIds
     * @return CampaignInterface
     */
    public function setCustomerGroupIds($customerGroupIds): CampaignInterface
    {
        return $this->setData(CampaignInterface::CUSTOMER_GROUP_IDS, $customerGroupIds);
    }

    /**
     * Get Restrict To Current Product
     *
     * @return string|null
     */
    public function getRestrictToCurrentProduct(): ?string
    {
        return $this->getData(CampaignInterface::RESTRICT_TO_CURRENT_PRODUCT);
    }

    /**
     * Set Restrict To Current Product
     *
     * @param string $restrictToCurrentProduct
     * @return CampaignInterface
     */
    public function setRestrictToCurrentProduct($restrictToCurrentProduct): CampaignInterface
    {
        return $this->setData(CampaignInterface::RESTRICT_TO_CURRENT_PRODUCT, $restrictToCurrentProduct);
    }

    /**
     * Get Period
     *
     * @return string|null
     */
    public function getPeriod(): ?string
    {
        return $this->getData(CampaignInterface::PERIOD);
    }

    /**
     * Set Period
     *
     * @param string $period
     * @return CampaignInterface
     */
    public function setPeriod($period): CampaignInterface
    {
        return $this->setData(CampaignInterface::PERIOD, $period);
    }

    /**
     * Get Start Delay
     *
     * @return string|null
     */
    public function getStartDelay(): ?string
    {
        return $this->getData(CampaignInterface::START_DELAY);
    }

    /**
     * Set Start Delay
     *
     * @param string $startDelay
     * @return CampaignInterface
     */
    public function setStartDelay($startDelay): CampaignInterface
    {
        return $this->setData(CampaignInterface::START_DELAY, $startDelay);
    }

    /**
     * Get Auto Close In
     *
     * @return string|null
     */
    public function getAutoCloseIn(): ?string
    {
        return $this->getData(CampaignInterface::AUTO_CLOSE_IN);
    }

    /**
     * Set Auto Close In
     *
     * @param string $autoCloseIn
     * @return CampaignInterface
     */
    public function setAutoCloseIn($autoCloseIn): CampaignInterface
    {
        return $this->setData(CampaignInterface::AUTO_CLOSE_IN, $autoCloseIn);
    }

    /**
     * Get Min Number Of Views
     *
     * @return string|null
     */
    public function getMinNumberOfViews(): ?string
    {
        return $this->getData(CampaignInterface::MIN_NUMBER_OF_VIEWS);
    }

    /**
     * Set Min Number Of Views
     *
     * @param string $minNumberOfViews
     * @return CampaignInterface
     */
    public function setMinNumberOfViews($minNumberOfViews): CampaignInterface
    {
        return $this->setData(CampaignInterface::MIN_NUMBER_OF_VIEWS, $minNumberOfViews);
    }

    /**
     * Get Max Number Per Page
     *
     * @return string|null
     */
    public function getMaxNumberPerPage(): ?string
    {
        return $this->getData(CampaignInterface::MAX_NUMBER_PER_PAGE);
    }

    /**
     * Set Max Number Per Page
     *
     * @param string $maxNumberPerPage
     * @return CampaignInterface
     */
    public function setMaxNumberPerPage($maxNumberPerPage): CampaignInterface
    {
        return $this->setData(CampaignInterface::MAX_NUMBER_PER_PAGE, $maxNumberPerPage);
    }

    /**
     * Get Display On
     *
     * @return array|null
     */
    public function getDisplayOn(): ?array
    {
        return $this->getData(CampaignInterface::DISPLAY_ON);
    }

    /**
     * Set Display On
     *
     * @param array $displayOn
     * @return CampaignInterface
     */
    public function setDisplayOn($displayOn): CampaignInterface
    {
        return $this->setData(CampaignInterface::DISPLAY_ON, $displayOn);
    }

    /**
     * Get Priority
     *
     * @return string|null
     */
    public function getPriority(): ?string
    {
        return $this->getData(CampaignInterface::PRIORITY);
    }

    /**
     * Set Priority
     *
     * @param string $priority
     * @return CampaignInterface
     */
    public function setPriority($priority): CampaignInterface
    {
        return $this->setData(CampaignInterface::PRIORITY, $priority);
    }

    /**
     * Get Remove Verified
     *
     * @return string|null
     */
    public function getRemoveVerified(): ?string
    {
        return $this->getData(CampaignInterface::REMOVE_VERIFIED);
    }

    /**
     * Set Remove Verified
     *
     * @param string $removeVerified
     * @return CampaignInterface
     */
    public function setRemoveVerified($removeVerified): CampaignInterface
    {
        return $this->setData(CampaignInterface::REMOVE_VERIFIED, $removeVerified);
    }

    /**
     * Get Product Ids
     *
     * @return array|null
     */
    public function getProductIds(): ?array
    {
        return $this->getData(CampaignInterface::PRODUCT_IDS);
    }

    /**
     * Set Product Ids
     *
     * @param array $productIds
     * @return CampaignInterface
     */
    public function setProductIds($productIds): CampaignInterface
    {
        return $this->setData(CampaignInterface::PRODUCT_IDS, $productIds);
    }

    /**
     * Get Category Ids
     *
     * @return array|null
     */
    public function getCategoryIds(): ?array
    {
        return $this->getData(CampaignInterface::CATEGORY_IDS);
    }

    /**
     * Set Category Ids
     *
     * @param array $categoryIds
     * @return CampaignInterface
     */
    public function setCategoryIds($categoryIds): CampaignInterface
    {
        return $this->setData(CampaignInterface::CATEGORY_IDS, $categoryIds);
    }

    /**
     * Get CMS Page Ids
     *
     * @return array|null
     */
    public function getCmsPageIds(): ?array
    {
        return $this->getData(CampaignInterface::CMS_PAGE_IDS);
    }

    /**
     * Set CMS Page Ids
     *
     * @param array $cmsPageIds
     * @return CampaignInterface
     */
    public function setCmsPageIds($cmsPageIds): CampaignInterface
    {
        return $this->setData(CampaignInterface::CMS_PAGE_IDS, $cmsPageIds);
    }

    /**
     * Get Position
     *
     * @return string|null
     */
    public function getPosition(): ?string
    {
        return $this->getData(CampaignInterface::POSITION);
    }

    /**
     * Set Position
     *
     * @param string $position
     *
     * @return CampaignInterface
     */
    public function setPosition($position): CampaignInterface
    {
        return $this->setData(CampaignInterface::POSITION, $position);
    }

    /**
     * Get Content
     *
     * @return string|null
     */
    public function getContent(): ?string
    {
        return $this->getData(CampaignInterface::CONTENT);
    }

    /**
     * Set Content
     *
     * @param string $content
     *
     * @return CampaignInterface
     */
    public function setContent($content): CampaignInterface
    {
        return $this->setData(CampaignInterface::CONTENT, $content);
    }

    /**
     * Get Products Assign Type
     *
     * @return string|null
     */
    public function getProductsAssignType(): ?string
    {
        return $this->getData(CampaignInterface::PRODUCTS_ASSIGN_TYPE);
    }

    /**
     * Set Products Assign Type
     *
     * @param string $productsAssignType
     * @return CampaignInterface
     */
    public function setProductsAssignType($productsAssignType): CampaignInterface
    {
        return $this->setData(CampaignInterface::PRODUCTS_ASSIGN_TYPE, $productsAssignType);
    }

    /**
     * Get Display On Products Conditions Serialized
     *
     * @return string|null
     */
    public function getDisplayOnProductsConditionsSerialized(): ?string
    {
        return $this->getData(CampaignInterface::DISPLAY_ON_PRODUCTS_CONDITIONS_SERIALIZED);
    }

    /**
     * Set Display On Products Conditions Serialized
     *
     * @param string $conditionsSerialized
     *
     * @return CampaignInterface
     */
    public function setDisplayOnProductsConditionsSerialized($conditionsSerialized): CampaignInterface
    {
        return $this->setData(CampaignInterface::DISPLAY_ON_PRODUCTS_CONDITIONS_SERIALIZED, $conditionsSerialized);
    }

    /**
     * Get Categories Assign Type
     *
     * @return string|null
     */
    public function getCategoriesAssignType(): ?string
    {
        return $this->getData(CampaignInterface::CATEGORIES_ASSIGN_TYPE);
    }

    /**
     * Set Categories Assign Type
     *
     * @param string $categoriesAssignType
     * @return CampaignInterface
     */
    public function setCategoriesAssignType($categoriesAssignType): CampaignInterface
    {
        return $this->setData(CampaignInterface::CATEGORIES_ASSIGN_TYPE, $categoriesAssignType);
    }

    /**
     * Get CMS Pages Assign Type
     *
     * @return string|null
     */
    public function getCmsPagesAssignType(): ?string
    {
        return $this->getData(CampaignInterface::CMS_PAGES_ASSIGN_TYPE);
    }

    /**
     * Set CMS Pages Assign Type
     *
     * @param string $cmsPagesAssignType
     * @return CampaignInterface
     */
    public function setCmsPagesAssignType($cmsPagesAssignType): CampaignInterface
    {
        return $this->setData(CampaignInterface::CMS_PAGES_ASSIGN_TYPE, $cmsPagesAssignType);
    }

    /**
     * Get Restriction Conditions Serialized
     *
     * @return string|null
     */
    public function getRestrictionConditionsSerialized(): ?string
    {
        return $this->getData(CampaignInterface::RESTRICTION_CONDITIONS_SERIALIZED);
    }

    /**
     * Set Restriction Conditions Serialized
     *
     * @param string $conditionsSerialized
     *
     * @return CampaignInterface
     */
    public function setRestrictionConditionsSerialized($conditionsSerialized): CampaignInterface
    {
        return $this->setData(CampaignInterface::RESTRICTION_CONDITIONS_SERIALIZED, $conditionsSerialized);
    }

    /**
     * @return CampaignExtensionInterface|null
     */
    public function getExtensionAttributes(): ?CampaignExtensionInterface
    {
        return $this->_getExtensionAttributes();
    }

    /**
     * @param CampaignExtensionInterface $extensionAttributes
     * @return CampaignInterface
     */
    public function setExtensionAttributes(CampaignExtensionInterface $extensionAttributes): CampaignInterface
    {
        return $this->_setExtensionAttributes($extensionAttributes);
    }
}
