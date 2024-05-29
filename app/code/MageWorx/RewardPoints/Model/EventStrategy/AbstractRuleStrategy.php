<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\RewardPoints\Model\EventStrategy;

use MageWorx\RewardPoints\Model\Source\EmailTemplates as EmailTemplatesOptions;

/**
 * Class RuleStrategy
 */
abstract class AbstractRuleStrategy extends \MageWorx\RewardPoints\Model\AbstractEventStrategy
{
    /**
     * @var int
     */
    protected $ruleId;

    /**
     * @var \MageWorx\RewardPoints\Api\Data\RuleInterface
     */
    protected $rule;

    /**
     * @var \MageWorx\RewardPoints\Model\ResourceModel\Rule
     */
    protected $ruleResource;

    /**
     * @var bool
     */
    protected $strictRuleMode = true;

    /**
     * AbstractRuleStrategy constructor.
     *
     * @param \MageWorx\RewardPoints\Model\ResourceModel\Rule $ruleResource
     * @param \MageWorx\RewardPoints\Helper\Data $helperData
     * @param array $data
     */
    public function __construct(
        \MageWorx\RewardPoints\Model\ResourceModel\Rule $ruleResource,
        \MageWorx\RewardPoints\Helper\Data $helperData,
        array $data = []
    ) {
        $this->ruleResource = $ruleResource;
        parent::__construct($helperData, $data);
    }

    /**
     * @return int
     */
    public function getRuleId()
    {
        return $this->ruleId;
    }

    /**
     * @param int $ruleId
     * @return $this
     */
    public function setRuleId($ruleId)
    {
        $this->ruleId = $ruleId;

        return $this;
    }

    /**
     * @return \MageWorx\RewardPoints\Api\Data\RuleInterface
     */
    public function getRule()
    {
        return $this->rule;
    }

    /**
     * @param \Magento\Rule\Model\AbstractModel|null $rule
     * @return $this
     */
    public function setRule($rule = null)
    {
        if ($this->strictRuleMode && !($rule instanceof \Magento\Rule\Model\AbstractModel)) {
            throw new \UnexpectedValueException(
                'Argument should be an instance of \Magento\Rule\Model\AbstractModel for the current event strategy.'
            );
        }

        if ($rule) {
            $this->rule = $rule;
            $this->setRuleId($rule->getRuleId());
        }

        return $this;
    }

    /**
     * @return double
     */
    public function getPoints()
    {
        if (!$this->pointAmount) {
            return $this->loadPointsFromRule();
        }

        return parent::getPoints();
    }

    /**
     * @return float|null
     */
    protected function loadPointsFromRule()
    {
        $rule = $this->getRule();

        if ($rule && $rule->getCalculationType() == \MageWorx\RewardPoints\Model\Rule::CALCULATION_TYPE_FIXED) {

            return $rule->getPointsAmount();
        }

        return null;
    }

    /**
     * @return bool
     */
    public function getIsPossibleSendNotification()
    {
        if (!$this->getRule()->getIsAllowNotification()) {
            return false;
        }

        return parent::getIsPossibleSendNotification();
    }

    /**
     * @return string
     */
    public function getEmailTemplateId()
    {
        if ($this->getRule()->getEmailTemplateId() == EmailTemplatesOptions::BY_CONFIG_VALUE) {
            return parent::getEmailTemplateId();
        }

        return $this->getRule()->getEmailTemplateId();
    }

    /**
     * @return array
     */
    public function getEventData()
    {
        return $this->getRuleEventData();
    }

    /**
     * @return array
     */
    protected function getRuleEventData()
    {
        return [
            'rule_id'   => $this->getRule()->getRuleId(),
            'rule_name' => $this->getRule()->getName()
        ];
    }

    /**
     * @param \MageWorx\RewardPoints\Api\Data\PointTransactionInterface $pointTransaction
     * @return $this
     */
    public function processAfterTransactionSave($pointTransaction)
    {
        if ($pointTransaction->getId() && $pointTransaction->getPointsDelta()) {
            $this->ruleResource->updateRuleUsage($pointTransaction);
        }

        return parent::processAfterTransactionSave($pointTransaction);
    }
}