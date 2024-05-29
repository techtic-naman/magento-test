<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace MageWorx\SocialProofBase\Observer;

use Magento\Catalog\Model\ResourceModel\Eav\Attribute as EavAttribute;

class UpdateCampaignAfterSaveAttributeObserver extends UpdateCampaignAfterDeleteAttributeObserver
{
    /**
     * @param EavAttribute $attribute
     * @return bool
     */
    protected function isNeedUpdate($attribute): bool
    {
        return $attribute->dataHasChangedFor('is_used_for_promo_rules') && !$attribute->getIsUsedForPromoRules();
    }
}
