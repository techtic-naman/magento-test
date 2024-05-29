<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\RewardPoints\Api\Data;

/**
 * Interface RuleLabelInterface
 */
interface RuleLabelInterface
{
    /**
     * Get storeId
     *
     * @return int
     */
    public function getStoreId();

    /**
     * Set store id
     *
     * @param int $storeId
     * @return $this
     */
    public function setStoreId($storeId);

    /**
     * Return the label for the store
     *
     * @return string
     */
    public function getStoreLabel();

    /**
     * Set the label for the store
     *
     * @param string $storeLabel
     * @return $this
     */
    public function setStoreLabel($storeLabel);
}
