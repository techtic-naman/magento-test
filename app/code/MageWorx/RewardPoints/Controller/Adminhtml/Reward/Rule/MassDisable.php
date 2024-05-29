<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\RewardPoints\Controller\Adminhtml\Reward\Rule;

use MageWorx\RewardPoints\Model\Rule;

class MassDisable extends MassAction
{

    /**
     * {@inheritdoc}
     */
    protected function executeAction(Rule $rule)
    {
        $rule->setIsActive($this->getActionValue());
        $this->ruleRepository->saveRule($rule);

        return $this;
    }

    /**
     * @return int
     */
    protected function getActionValue()
    {
        return Rule::STATUS_DISABLED;
    }

    /**
     * {@inheritdoc}
     */
    protected function getSuccessMessage($size)
    {
        return __('A total of %1 Rule(s) have been disabled.', $size);
    }

    /**
     * {@inheritdoc}
     */
    protected function getErrorMessage()
    {
        return __('An error occurred while disabling rule(s).');
    }
}
