<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\RewardPoints\Model\Data;

class RuleLabel extends \Magento\Framework\Api\AbstractSimpleObject implements
    \MageWorx\RewardPoints\Api\Data\RuleLabelInterface
{
    const KEY_STORE_ID    = 'store_id';
    const KEY_STORE_LABEL = 'store_label';

    /**
     * Get storeId
     *
     * @return int
     */
    public function getStoreId()
    {
        return $this->_get(self::KEY_STORE_ID);
    }

    /**
     * Return the label for the store
     *
     * @return string
     */
    public function getStoreLabel()
    {
        return $this->_get(self::KEY_STORE_LABEL);
    }

    /**
     * Set store id
     *
     * @param int $storeId
     * @return $this
     */
    public function setStoreId($storeId)
    {
        return $this->setData(self::KEY_STORE_ID, $storeId);
    }

    /**
     * Set the label for the store
     *
     * @param string $storeLabel
     * @return $this
     */
    public function setStoreLabel($storeLabel)
    {
        return $this->setData(self::KEY_STORE_LABEL, $storeLabel);
    }
}
