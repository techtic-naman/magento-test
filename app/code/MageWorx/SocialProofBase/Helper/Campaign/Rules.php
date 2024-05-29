<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace MageWorx\SocialProofBase\Helper\Campaign;

use Magento\CatalogRule\Model\RuleFactory as CatalogRuleFactory;
use Magento\Framework\App\Helper\Context;
use Magento\CatalogRule\Model\Rule as CatalogRule;
use MageWorx\SocialProofBase\Api\Data\CampaignInterface;
use MageWorx\SocialProofBase\Model\Campaign\RestrictionRule;
use MageWorx\SocialProofBase\Model\Campaign\RestrictionRuleFactory;

class Rules extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var RestrictionRule|null
     */
    protected $restrictionRule;

    /**
     * @var CatalogRule
     */
    protected $catalogRule;

    /**
     * @var CatalogRuleFactory
     */
    protected $catalogRuleFactory;

    /**
     * @var RestrictionRuleFactory
     */
    protected $restrictionRuleFactory;

    /**
     * Rules constructor.
     *
     * @param Context $context
     * @param CatalogRuleFactory $catalogRuleFactory
     * @param RestrictionRuleFactory $restrictionRuleFactory
     */
    public function __construct(
        Context $context,
        CatalogRuleFactory $catalogRuleFactory,
        RestrictionRuleFactory $restrictionRuleFactory
    ) {
        parent::__construct($context);

        $this->catalogRuleFactory     = $catalogRuleFactory;
        $this->restrictionRuleFactory = $restrictionRuleFactory;
    }

    /**
     * @param CampaignInterface|null $campaign
     * @return RestrictionRule
     */
    public function getRestrictionRuleModel($campaign = null): RestrictionRule
    {
        if (!$this->restrictionRule) {

            $this->restrictionRule = $this->restrictionRuleFactory->create();

            if ($campaign) {
                $conditionsSerialized = $campaign->getRestrictionConditionsSerialized();

                if ($conditionsSerialized) {
                    $this->restrictionRule->setConditionsSerialized($conditionsSerialized);
                }
            } else {
                $this->restrictionRule->getConditions()->setConditions([]);
            }
        }

        return $this->restrictionRule;
    }

    /**
     * @param CampaignInterface|null $campaign
     * @return CatalogRule
     */
    public function getDisplayOnProductsRuleModel($campaign = null): CatalogRule
    {
        if (!$this->catalogRule) {

            $this->catalogRule = $this->catalogRuleFactory->create();

            if ($campaign) {
                $conditionsSerialized = $campaign->getDisplayOnProductsConditionsSerialized();

                if ($conditionsSerialized) {
                    $this->catalogRule->setConditionsSerialized($conditionsSerialized);
                }
            }
        }

        return $this->catalogRule;
    }
}
