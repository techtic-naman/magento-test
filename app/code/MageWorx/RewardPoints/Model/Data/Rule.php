<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\RewardPoints\Model\Data;

use Magento\SalesRule\Api\Data\ConditionInterface;
use MageWorx\RewardPoints\Api\Data\RuleInterface;

class Rule extends \Magento\Framework\Api\AbstractExtensibleObject implements RuleInterface
{
    const KEY_RULE_ID               = 'rule_id';
    const KEY_NAME                  = 'name';
    const KEY_DESCRIPTION           = 'description';
    const KEY_STORE_LABELS          = 'store_labels';
    const KEY_FROM_DATE             = 'from_date';
    const KEY_TO_DATE               = 'to_date';
    const KEY_IS_ACTIVE             = 'is_active';
    const KEY_CONDITION             = 'condition_json';
    const KEY_ACTION_CONDITION      = 'action_json';
    const KEY_STOP_RULES_PROCESSING = 'stop_rules_processing';
    const KEY_WEBSITES              = 'website_ids';
    const KEY_CUSTOMER_GROUPS       = 'customer_group_ids';
    const KEY_SORT_ORDER            = 'sort_order';
    const KEY_EVENT                 = 'event';
    const KEY_SIMPLE_ACTION         = 'simple_action';
    const KEY_CALCULATION_TYPE      = 'calculation_type';
    const KEY_POINTS_AMOUNT         = 'points_amount';
    const KEY_POINTS_STEP           = 'points_step';
    const KEY_POINT_STAGE           = 'point_stage';
    const KEY_TIMES_USED            = 'times_used';
    const KEY_MAX_TIMES_USED        = 'max_times_used';
    const KEY_POINTS_GENERATED      = 'points_generated';
    const KEY_MAX_POINTS_GENERATED  = 'max_points_generated';
    const KEY_IS_RSS                = 'is_rss';
    const KEY_IS_ALLOW_NOTIFICATION = 'is_allow_notification';
    const KEY_EMAIL_TEMPLATE_ID     = 'email_template_id';

    /**
     * {@inheritdoc}
     */
    public function getRuleId()
    {
        return $this->_get(self::KEY_RULE_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setRuleId($ruleId)
    {
        return $this->setData(self::KEY_RULE_ID, $ruleId);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->_get(self::KEY_NAME);
    }

    /**
     * {@inheritdoc}
     */
    public function setName($name)
    {
        return $this->setData(self::KEY_NAME, $name);
    }

    /**
     * {@inheritdoc}
     */
    public function getStoreLabels()
    {
        return $this->_get(self::KEY_STORE_LABELS);
    }

    /**
     * {@inheritdoc}
     */
    public function setStoreLabels(array $storeLabels = null)
    {
        return $this->setData(self::KEY_STORE_LABELS, $storeLabels);
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return $this->_get(self::KEY_DESCRIPTION);
    }

    /**
     * {@inheritdoc}
     */
    public function setDescription($description)
    {
        return $this->setData(self::KEY_DESCRIPTION, $description);
    }

    /**
     * {@inheritdoc}
     */
    public function getWebsiteIds()
    {
        return $this->_get(self::KEY_WEBSITES);
    }

    /**
     * {@inheritdoc}
     */
    public function setWebsiteIds(array $websiteIds)
    {
        return $this->setData(self::KEY_WEBSITES, $websiteIds);
    }

    /**
     * {@inheritdoc}
     */
    public function getCustomerGroupIds()
    {
        return $this->_get(self::KEY_CUSTOMER_GROUPS);
    }

    /**
     * {@inheritdoc}
     */
    public function setCustomerGroupIds(array $customerGroupIds)
    {
        return $this->setData(self::KEY_CUSTOMER_GROUPS, $customerGroupIds);
    }

    /**
     * {@inheritdoc}
     */
    public function getFromDate()
    {
        return $this->_get(self::KEY_FROM_DATE);
    }

    /**
     * {@inheritdoc}
     */
    public function setFromDate($fromDate)
    {
        return $this->setData(self::KEY_FROM_DATE, $fromDate);
    }

    /**
     * {@inheritdoc}
     */
    public function getToDate()
    {
        return $this->_get(self::KEY_TO_DATE);
    }

    /**
     * {@inheritdoc}
     */
    public function setToDate($toDate)
    {
        return $this->setData(self::KEY_TO_DATE, $toDate);
    }

    /**
     * {@inheritdoc}
     */
    public function getIsActive()
    {
        return $this->_get(self::KEY_IS_ACTIVE);
    }


    /**
     * {@inheritdoc}
     */
    public function setIsActive($isActive)
    {
        return $this->setData(self::KEY_IS_ACTIVE, $isActive);
    }

    /**
     * {@inheritdoc}
     */
    public function getTimesUsed()
    {
        return $this->_get(self::KEY_TIMES_USED);
    }

    /**
     * {@inheritdoc}
     */
    public function setTimesUsed($num)
    {
        return $this->setData(self::KEY_TIMES_USED, $num);
    }

    /**
     * {@inheritdoc}
     */
    public function getMaxTimesUsed()
    {
        return $this->_get(self::KEY_MAX_TIMES_USED);
    }

    /**
     * {@inheritdoc}
     */
    public function setMaxTimesUsed($num)
    {
        return $this->setData(self::KEY_MAX_TIMES_USED, $num);
    }

    /**
     * {@inheritdoc}
     */
    public function getPointsGenerated()
    {
        return $this->_get(self::KEY_POINTS_GENERATED);
    }

    /**
     * {@inheritdoc}
     */
    public function setPointsGenerated($num)
    {
        return $this->setData(self::KEY_POINTS_GENERATED, $num);
    }

    /**
     * {@inheritdoc}
     */
    public function getMaxPointsGenerated()
    {
        return $this->_get(self::KEY_POINTS_GENERATED);
    }

    /**
     * {@inheritdoc}
     */
    public function setMaxPointsGenerated($num)
    {
        return $this->setData(self::KEY_POINTS_GENERATED, $num);
    }

    /**
     * {@inheritdoc}
     */
    public function getCondition()
    {
        return $this->_get(self::KEY_CONDITION);
    }

    /**
     * {@inheritdoc}
     */
    public function setCondition(ConditionInterface $condition = null)
    {
        return $this->setData(self::KEY_CONDITION, $condition);
    }

    /**
     * {@inheritdoc}
     */
    public function getActionCondition()
    {
        return $this->_get(self::KEY_ACTION_CONDITION);
    }

    /**
     * {@inheritdoc}
     */
    public function setActionCondition(ConditionInterface $actionCondition = null)
    {
        return $this->setData(self::KEY_ACTION_CONDITION, $actionCondition);
    }

    /**
     * {@inheritdoc}
     */
    public function getSortOrder()
    {
        return $this->_get(self::KEY_SORT_ORDER);
    }

    /**
     * {@inheritdoc}
     */
    public function setSortOrder($sortOrder)
    {
        return $this->setData(self::KEY_SORT_ORDER, $sortOrder);
    }

    /**
     * {@inheritdoc}
     */
    public function getEvent()
    {
        return $this->_get(self::KEY_EVENT);
    }

    /**
     * {@inheritdoc}
     */
    public function setEvent($event)
    {
        return $this->setData(self::KEY_EVENT, $event);
    }

    /**
     * {@inheritdoc}
     */
    public function getSimpleAction()
    {
        return $this->_get(self::KEY_SIMPLE_ACTION);
    }

    /**
     * {@inheritdoc}
     */
    public function setSimpleAction($simpleAction)
    {
        return $this->setData(self::KEY_SIMPLE_ACTION, $simpleAction);
    }

    /**
     * {@inheritdoc}
     */
    public function getCalculationType()
    {
        return $this->_get(self::KEY_CALCULATION_TYPE);
    }

    /**
     * {@inheritdoc}
     */
    public function setCalculationType($calculationType)
    {
        return $this->setData(self::KEY_CALCULATION_TYPE, $calculationType);
    }

    /**
     * {@inheritdoc}
     */
    public function getPointsAmount()
    {
        return $this->_get(self::KEY_POINTS_AMOUNT);
    }

    /**
     * {@inheritdoc}
     */
    public function setPointsAmount($pointsAmount)
    {
        return $this->setData(self::KEY_POINTS_AMOUNT, $pointsAmount);
    }

    /**
     * {@inheritdoc}
     */
    public function getPointsStep()
    {
        return $this->_get(self::KEY_POINTS_STEP);
    }

    /**
     * {@inheritdoc}
     */
    public function setPointsStep($pointsStep)
    {
        return $this->setData(self::KEY_POINTS_STEP, $pointsStep);
    }

    /**
     * {@inheritdoc}
     */
    public function getPointStage()
    {
        return $this->_get(self::KEY_POINT_STAGE);
    }

    /**
     * {@inheritdoc}
     */
    public function setPointStage($pointStage)
    {
        return $this->setData(self::KEY_POINT_STAGE, $pointStage);
    }

    /**
     * {@inheritdoc}
     */
    public function getStopRulesProcessing()
    {
        return $this->_get(self::KEY_STOP_RULES_PROCESSING);
    }

    /**
     * {@inheritdoc}
     */
    public function setStopRulesProcessing($stopRulesProcessing)
    {
        return $this->setData(self::KEY_STOP_RULES_PROCESSING, $stopRulesProcessing);
    }

    /**
     * {@inheritdoc}
     */
    public function getIsRss()
    {
        return $this->_get(self::KEY_IS_RSS);
    }

    /**
     * {@inheritdoc}
     */
    public function setIsRss($isRss)
    {
        return $this->setData(self::KEY_IS_RSS, $isRss);
    }

    /**
     * {@inheritdoc}
     */
    public function getIsAllowNotification()
    {
        return (bool)$this->_get(self::KEY_IS_ALLOW_NOTIFICATION);
    }

    /**
     * {@inheritdoc}
     */
    public function setIsAllowNotification($value)
    {
        return $this->setData(self::KEY_IS_ALLOW_NOTIFICATION, (bool)$value);
    }

    /**
     * {@inheritdoc}
     */
    public function getEmailTemplateId()
    {
        return $this->_get(self::KEY_EMAIL_TEMPLATE_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setEmailTemplateId($emailTemplateId)
    {
        return $this->setData(self::KEY_EMAIL_TEMPLATE_ID, $emailTemplateId);
    }

    /**
     * {@inheritdoc}
     */
    public function getExtensionAttributes()
    {
        return $this->_getExtensionAttributes();
    }

    /**
     * {@inheritdoc}
     *
     */
    public function setExtensionAttributes(
        \MageWorx\RewardPoints\Api\Data\RuleExtensionInterface $extensionAttributes
    ) {
        return $this->_setExtensionAttributes($extensionAttributes);
    }
}