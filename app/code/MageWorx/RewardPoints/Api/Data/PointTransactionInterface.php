<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\RewardPoints\Api\Data;

/**
 * Interface PointTransactionInterface
 */
interface PointTransactionInterface
{
    /**#@+
     * Constants for keys of data array. Identical to the name of the getter in snake case
     */
    const TRANSACTION_ID       = 'transaction_id';
    const CUSTOMER_BALANCE_ID  = 'customer_balance_id';
    const CUSTOMER_ID          = 'customer_id';
    const WEBSITE_ID           = 'website_id';
    const STORE_ID             = 'store_id';
    const POINTS_BALANCE       = 'points_balance';
    const POINTS_DELTA         = 'points_delta';
    const EVENT_CODE           = 'event_code';
    const EVENT_DATA           = 'event_data';
    const RULE_ID              = 'rule_id';
    const ENTITY_ID            = 'entity_id';
    const CREATED_AT           = 'created_at';
    const IS_NOTIFICATION_SENT = 'is_notification_sent';
    const EXPIRATION_PERIOD    = 'expiration_period';

    /** @since 1.5.0 */
    const COMMENT = 'comment';

    /** @since 1.5.0 */
    const IS_NEED_SEND_NOTIFICATION = 'is_need_send_notification';
    /**#@-*/

    /**#@+
     * Constants for keys of unserialized Event Data property.
     */
    const EVENT_DATA_RULE_ID = 'rule_id';
    /** @deprecated Not used anymore */
    const EVENT_DATA_COMMENT_KEY = 'comment';
    const EVENT_DATA_RATE        = 'rate';
    /**#@-*/

    /**
     * @return int|null
     */
    public function getTransactionId();

    /**
     * @param int $transactionId
     * @return $this
     */
    public function setTransactionId($transactionId);

    /**
     * @return int|null
     */
    public function getCustomerBalanceId();

    /**
     * @param int $customerBalanceId
     * @return $this
     */
    public function setCustomerBalanceId($customerBalanceId);

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
     * @return int|null
     */
    public function getStoreId();

    /**
     * @param int $storeId
     * @return $this
     */
    public function setStoreId($storeId);

    /**
     * @return double|null
     */
    public function getPointsBalance();

    /**
     * @param double $pointsBalance
     * @return $this
     */
    public function setPointsBalance($pointsBalance);

    /**
     * @return double|null
     */
    public function getPointsDelta();

    /**
     * @param double $pointsDelta
     * @return $this
     */
    public function setPointsDelta($pointsDelta);

    /**
     * @return string|null
     */
    public function getEventCode();

    /**
     * @param string $eventCode
     * @return $this
     */
    public function setEventCode($eventCode);

    /**
     * @return mixed[]
     */
    public function getEventData();

    /**
     * @param mixed[] $eventData
     * @return $this
     */
    public function setEventData($eventData);

    /**
     * @return int|null
     */
    public function getRuleId();

    /**
     * @param int|null $ruleId
     * @return $this
     */
    public function setRuleId($ruleId);

    /**
     * @return int|null
     */
    public function getEntityId();

    /**
     * @param int|null $entityId
     * @return $this
     */
    public function setEntityId($entityId);

    /**
     * @return string
     */
    public function getCreatedAt();

    /**
     * @return bool
     */
    public function getIsNotificationSent();

    /**
     * @param bool $flag
     * @return $this
     */
    public function setIsNotificationSent($flag);

    /**
     * Retrieve expiration period in days
     *
     * @return mixed
     */
    public function getExpirationPeriod();

    /**
     * Set expiration period in days
     *
     * @param int $days
     * @return mixed
     */
    public function setExpirationPeriod($days);

    /**
     * @return string
     * @since 1.5.0
     */
    public function getComment();


    /**
     * @param string $comment
     * @return $this
     * @since 1.5.0
     */
    public function setComment($comment);

    /**
     * @return bool
     * @since 1.5.0
     */
    public function getIsNeedSendNotification();

    /**
     * @param bool $flag
     * @return $this
     * @since 1.5.0
     */
    public function setIsNeedSendNotification($flag);
}
