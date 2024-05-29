<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\RewardPoints\Observer\Rule;

class UpdateRuleAfterSaveAttributeObserver extends UpdateRuleAfterDeleteAttributeObserver
{
    /**
     * @param \Magento\Catalog\Model\ResourceModel\Eav\Attribute $attribute
     * @return mixed
     */
    protected function getIsNeedCheck($attribute)
    {
        return $attribute->dataHasChangedFor('is_used_for_promo_rules') && !$attribute->getIsUsedForPromoRules();
    }
}
