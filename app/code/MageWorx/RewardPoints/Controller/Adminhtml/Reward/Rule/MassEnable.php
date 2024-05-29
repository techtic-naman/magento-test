<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\RewardPoints\Controller\Adminhtml\Reward\Rule;

use MageWorx\RewardPoints\Model\Rule;

class MassEnable extends MassAction
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
        return Rule::STATUS_ENABLED;
    }

    /**
     * {@inheritdoc}
     */
    protected function getSuccessMessage($size)
    {
        return __('A total of %1 Rule(s) have been enabled.', $size);
    }

    /**
     * {@inheritdoc}
     */
    protected function getErrorMessage()
    {
        return __('An error occurred while enabling Rule(s).');
    }
}
