<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace MageWorx\SocialProofBase\Model\Campaign;

use Magento\Rule\Model\Condition\Combine as ConditionCombine;

class RestrictionRule extends \Magento\CatalogRule\Model\Rule
{
    const CONDITIONS_PREFIX = 'actions';

    /**
     * Reset rule combine conditions
     *
     * @param ConditionCombine|null $conditions
     * @return $this
     */
    protected function _resetConditions($conditions = null): RestrictionRule
    {
        if (is_null($conditions)) {
            $conditions = $this->getConditionsInstance();
        }

        $conditions->setRule($this)->setId('1')->setPrefix(self::CONDITIONS_PREFIX);
        $this->setConditions($conditions);

        return $this;
    }
}
