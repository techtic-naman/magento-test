<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\RewardPoints\Model\ResourceModel\PointTransaction;


class Collection extends \MageWorx\RewardPoints\Model\ResourceModel\PointTransaction\AbstractCollection
{
    /**
     * {@inheritdoc}
     */
    protected function _afterLoad()
    {
        foreach ($this->_items as $item) {
            $this->getResource()->unserializeFields($item);
        }

        return parent::_afterLoad();
    }
}