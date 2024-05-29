<?php
/**
 * Copyright © 2018 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\PersonalPromotion\Helper;

use Magento\Framework\App\Helper\Context;
use MageWorx\PersonalPromotion\Helper\Data as HelperData;
use MageWorx\PersonalPromotion\Model\ResourceModel\PersonalPromotion as PersonalPromotionResourceModel;
use Magento\SalesRule\Api\Data\RuleInterface;
use MageWorx\PersonalPromotion\Helper\LinkFieldResolver as HelperLinkFieldResolver;

class Rule extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var HelperData
     */
    protected $helperData;

    /**
     * @var HelperLinkFieldResolver
     */
    protected $helperLinkFieldResolver;

    /**
     * @var PersonalPromotionResourceModel
     */
    protected $personalPromotionResourceModel;

    /**
     * Rule constructor.
     *
     * @param Context $context
     * @param Data $helperData
     * @param HelperLinkFieldResolver $helperLinkFieldResolver
     * @param PersonalPromotionResourceModel $personalPromotionResourceModel
     */
    public function __construct(
        Context $context,
        HelperData $helperData,
        HelperLinkFieldResolver $helperLinkFieldResolver,
        PersonalPromotionResourceModel $personalPromotionResourceModel
    ) {
        $this->personalPromotionResourceModel = $personalPromotionResourceModel;
        $this->helperData                     = $helperData;
        $this->helperLinkFieldResolver        = $helperLinkFieldResolver;
        parent::__construct($context);
    }

    /**
     * @param array $params
     * @return null|string
     * @throws \Zend_Db_Select_Exception
     */
    public function getRuleId($params, $rule = null)
    {
        $ruleFileldId = $this->helperLinkFieldResolver->getLinkField(RuleInterface::class);

        // Main form
        if ($rule) {
            return $rule->getData($ruleFileldId);
        }

        //Ajax - filters / sorting - From main or schedule update form.
        if (isset($params[$ruleFileldId])) {
            $ruleId = $params[$ruleFileldId];
        }
        //From schedule update form
        else {
            $ruleId = null;

            if (isset($params['id'])) {
                $ruleId = $params['id'];
            }

            if ($ruleId && !empty($params['handle']) && !empty($params['update_id'])) {
                $ruleId = $this->personalPromotionResourceModel->getRowIdByRuleId(
                    $ruleId,
                    $ruleFileldId,
                    $params['update_id']
                );
            }
        }

        return $ruleId;
    }
}
