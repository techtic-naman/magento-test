<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\RewardPoints\Api\Data;

/**
 * Interface CustomerBalanceInterface
 */
interface CustomerBalanceInterface
{
    /**
     * Get ID
     *
     * @return int|null
     */
    public function getId();

    /**
     * Set ID
     *
     * @param int $id
     * @return $this
     */
    public function setId($id);

    /**
     * @return int|null
     */
    public function getCustomerId();

    /**
     * @param int $customerId
     * @return $this
     */
    public function setCustomerId($customerId);

    /**
     * @return int|null
     */
    public function getWebsiteId();

    /**
     * @param int $websiteId
     * @return $this
     */
    public function setWebsiteId($websiteId);

    /**
     * @return double|null
     */
    public function getPoints();

    /**
     * @param double $points
     * @return $this
     */
    public function setPoints($points);

    /**
     * @return string|null
     */
    public function getExpirationDate();

    /**
     * @param string|null $date
     * @return mixed
     */
    public function setExpirationDate($date = null);
}
