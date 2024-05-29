<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\RewardPoints\Api\Data;

use Magento\SalesRule\Api\Data\ConditionInterface;

/**
 * Interface RuleInterface
 */
interface RuleInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{
    /**
     * Return rule id
     *
     * @return int|null
     */
    public function getRuleId();

    /**
     * Set rule id
     *
     * @param int $ruleId
     * @return $this
     */
    public function setRuleId($ruleId);

    /**
     * Get rule name
     *
     * @return string|null
     */
    public function getName();

    /**
     * Set rule name
     *
     * @param string $name
     * @return $this
     */
    public function setName($name);

    /**
     * Get display label
     *
     * @return \MageWorx\RewardPoints\Api\Data\RuleLabelInterface[]|null
     */
    public function getStoreLabels();

    /**
     * Set display label
     *
     * @param \MageWorx\RewardPoints\Api\Data\RuleLabelInterface[]|null $storeLabels
     * @return $this
     */
    public function setStoreLabels(array $storeLabels = null);

    /**
     * Get description
     *
     * @return string|null
     */
    public function getDescription();

    /**
     * Set description
     *
     * @param string $description
     * @return $this
     */
    public function setDescription($description);

    /**
     * Get a list of websites the rule applies to
     *
     * @return int[]
     */
    public function getWebsiteIds();

    /**
     * Set the websites the rule applies to
     *
     * @param int[] $websiteIds
     * @return $this
     */
    public function setWebsiteIds(array $websiteIds);

    /**
     * Get ids of customer groups that the rule applies to
     *
     * @return int[]
     */
    public function getCustomerGroupIds();

    /**
     * Set the customer groups that the rule applies to
     *
     * @param int[] $customerGroupIds
     * @return $this
     */
    public function setCustomerGroupIds(array $customerGroupIds);

    /**
     * Get the start date when the coupon is active
     *
     * @return string|null
     */
    public function getFromDate();

    /**
     * Set the star date when the coupon is active
     *
     * @param string $fromDate
     * @return $this
     */
    public function setFromDate($fromDate);

    /**
     * Get the end date when the coupon is active
     *
     * @return string|null
     */
    public function getToDate();

    /**
     * Set the end date when the rule is active
     *
     * @param string $fromDate
     * @return $this
     */
    public function setToDate($fromDate);

    /**
     * Whether the rule is active
     *
     * @return bool
     */
    public function getIsActive();

    /**
     * Set whether the coupon is active
     *
     * @param bool $isActive
     * @return bool
     */
    public function setIsActive($isActive);

    /**
     * Get the threshold after the rule will be disabled
     *
     * @return bool
     */
    public function getTimesUsed();

    /**
     * Set the number the rule was triggered by the customers
     *
     * @param int $num
     * @return bool
     */
    public function setTimesUsed($num);

    /**
     * Get the max number the rule can be triggered by the customers
     *
     * @return bool
     */
    public function getMaxTimesUsed();

    /**
     * Set the max number the rule can be triggered by the customers
     *
     * @param int $num
     * @return bool
     */
    public function setMaxTimesUsed($num);

    /**
     * Get the amount of generated points
     *
     * @return float
     */
    public function getPointsGenerated();

    /**
     * Set the amount of generated points
     *
     * @param float $value
     * @return bool
     */
    public function setPointsGenerated($value);

    /**
     * Get the generated points threshold after which the rule will be disabled
     *
     * @return bool
     */
    public function getMaxPointsGenerated();

    /**
     * Set the generated points threshold after which the rule will be disabled
     *
     * @param int $num
     * @return bool
     */
    public function setMaxPointsGenerated($value);

    /**
     * Get condition for the rule
     *
     * @return \Magento\SalesRule\Api\Data\ConditionInterface|null
     */
    public function getCondition();

    /**
     * Set condition for the rule
     *
     * @param \Magento\SalesRule\Api\Data\ConditionInterface|null $condition
     * @return $this
     */
    public function setCondition(ConditionInterface $condition = null);

    /**
     * Get action condition
     *
     * @return \Magento\SalesRule\Api\Data\ConditionInterface|null
     */
    public function getActionCondition();

    /**
     * Set action condition
     *
     * @param \Magento\SalesRule\Api\Data\ConditionInterface|null $actionCondition
     * @return $this
     */
    public function setActionCondition(ConditionInterface $actionCondition = null);

    /**
     * Whether to stop rule processing
     *
     * @return bool
     */
    public function getStopRulesProcessing();

    /**
     * Set whether to stop rule processing
     *
     * @param bool $stopRulesProcessing
     * @return $this
     */
    public function setStopRulesProcessing($stopRulesProcessing);

    /**
     * Get sort order
     *
     * @return int
     */
    public function getSortOrder();

    /**
     * @param int $sortOrder
     * @return $this
     */
    public function setSortOrder($sortOrder);

    /**
     * Get event of the rule
     *
     * @return string|null
     */
    public function getEvent();

    /**
     * Set event
     *
     * @param string $event
     * @return $this
     */
    public function setEvent($event);

    /**
     * Get simple action of the rule
     *
     * @return string|null
     */
    public function getSimpleAction();

    /**
     * Set simple action
     *
     * @param string $simpleAction
     * @return $this
     */
    public function setSimpleAction($simpleAction);

    /**
     * Get calculation type of the rule's simple action
     *
     * @return string
     */
    public function getCalculationType();

    /**
     * Set calculation type
     *
     * @param string $calculationType
     * @return $this
     */
    public function setCalculationType($calculationType);

    /**
     * Get points amount
     *
     * @return float
     */
    public function getPointsAmount();

    /**
     * Set points amount
     *
     * @param float $pointsAmount
     * @return $this
     */
    public function setPointsAmount($pointsAmount);

    /**
     * Get points step
     *
     * @return float
     */
    public function getPointsStep();

    /**
     * Set points step
     *
     * @param float $pointsStep
     * @return $this
     */
    public function setPointsStep($pointsStep);

    /**
     * Get point stage
     *
     * @return float
     */
    public function getPointStage();

    /**
     * Set point stage
     *
     * @param float $pointStage
     * @return $this
     */
    public function setPointStage($pointStage);

    /**
     * Return whether the rule is in RSS
     *
     * @return bool
     */
    public function getIsRss();

    /**
     * Set whether the rule is shown in RSS
     *
     * @param bool $isRss
     * @return $this
     */
    public function setIsRss($isRss);

    /**
     * Retrieve notification flag
     *
     * @return bool
     */
    public function getIsAllowNotification();

    /**
     * Set notification flag
     *
     * @param bool $value
     * @return $this
     */
    public function setIsAllowNotification($value);

    /**
     * Get email template ID
     *
     * @return int
     */
    public function getEmailTemplateId();

    /**
     * Set email template ID
     *
     * @param int|null $emailTemplateId
     * @return $this
     */
    public function setEmailTemplateId($emailTemplateId);

    /**
     * Retrieve existing extension attributes object or create a new one.
     *
     * @return \MageWorx\RewardPoints\Api\Data\RuleExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object.
     *
     * @param \MageWorx\RewardPoints\Api\Data\RuleExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(\MageWorx\RewardPoints\Api\Data\RuleExtensionInterface $extensionAttributes);
}
