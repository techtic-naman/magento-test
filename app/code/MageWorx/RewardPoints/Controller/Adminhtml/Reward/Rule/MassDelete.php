<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\RewardPoints\Controller\Adminhtml\Reward\Rule;

use MageWorx\RewardPoints\Model\Rule;

class MassDelete extends MassAction
{
    /**
     * {@inheritdoc}
     */
    protected function executeAction(Rule $rule)
    {
        $this->ruleRepository->deleteById($rule->getRuleId());

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    protected function getSuccessMessage($size)
    {
        return __('A total of %1 record(s) have been deleted.', $size);
    }

    /**
     * {@inheritdoc}
     */
    protected function getErrorMessage()
    {
        return __('An error occurred while deleting record(s).');
    }
}
