<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\RewardPoints\Block\Adminhtml\Reward\Rule\Edit\Tab;

class Conditions extends \Magento\SalesRule\Block\Adminhtml\Promo\Quote\Edit\Tab\Conditions
{
    /**
     * {@inheritdoc}
     */
    protected function _prepareForm()
    {
        $model = $this->_coreRegistry->registry(\MageWorx\RewardPoints\Model\RegistryConstants::CURRENT_REWARD_RULE);
        $form  = $this->addTabToForm($model, 'conditions_fieldset', 'mageworx_rewardpoints_rule_form');
        $this->setForm($form);

        return $this;
    }
}
