<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace MageWorx\CountdownTimersBase\Helper\CountdownTimer;

use Magento\CatalogRule\Model\RuleFactory as CatalogRuleFactory;
use Magento\Framework\App\Helper\Context;
use Magento\CatalogRule\Model\Rule as CatalogRule;
use MageWorx\CountdownTimersBase\Api\Data\CountdownTimerInterface;

class Rules extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var CatalogRule
     */
    protected $catalogRule;

    /**
     * @var CatalogRuleFactory
     */
    protected $catalogRuleFactory;

    /**
     * Rules constructor.
     *
     * @param Context $context
     * @param CatalogRuleFactory $catalogRuleFactory
     */
    public function __construct(
        Context $context,
        CatalogRuleFactory $catalogRuleFactory
    ) {
        parent::__construct($context);

        $this->catalogRuleFactory = $catalogRuleFactory;
    }

    /**
     * @param CountdownTimerInterface|null $countdownTimer
     * @return CatalogRule
     */
    public function getRuleModel($countdownTimer = null): CatalogRule
    {
        if (!$this->catalogRule) {

            $this->catalogRule = $this->catalogRuleFactory->create();

            if ($countdownTimer) {
                $conditionsSerialized = $countdownTimer->getConditionsSerialized();

                if ($conditionsSerialized) {
                    $this->catalogRule->setConditionsSerialized($conditionsSerialized);
                }
            }
        }

        return $this->catalogRule;
    }
}
