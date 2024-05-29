<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\RewardPoints\Model;

/**
 * Class EventStrategy
 *
 * @method string getEventCode()
 * @method \MageWorx\RewardPoints\Model\AbstractEventStrategy setEventCode(string $code)
 * @method int getStoreId()
 * @method \MageWorx\RewardPoints\Model\AbstractEventStrategy setStoreId(int $storeId)
 * @method int getWebsiteId()
 * @method \MageWorx\RewardPoints\Model\AbstractEventStrategy setWebsiteId(int $websiteId)
 * @method \Magento\Framework\DataObject getEntity()
 * @method \MageWorx\RewardPoints\Model\AbstractEventStrategy setEntity(\Magento\Framework\DataObject $entity)
 */
abstract class AbstractEventStrategy extends \Magento\Framework\DataObject
{
    /**
     * @var \MageWorx\RewardPoints\Helper\Data
     */
    protected $helperData;

    /**
     * @var double
     */
    protected $pointAmount;

    /**
     * EventStrategy constructor.
     *
     * @param \MageWorx\RewardPoints\Helper\Data $helperData
     * @param array $data
     */
    public function __construct(
        \MageWorx\RewardPoints\Helper\Data $helperData,
        array $data = []
    ) {
        $this->helperData = $helperData;
        parent::__construct($data);
    }

    /**
     * @return []
     */
    abstract public function getEventData();

    /**
     * @param array $eventData
     * @param string|null $comment
     * @return string
     */
    abstract public function getMessage(array $eventData, $comment = null);

    /**
     * @return bool
     */
    public function useImmediateSending()
    {
        return true;
    }

    /**
     * @return double
     */
    public function getPoints()
    {
        return $this->pointAmount;
    }

    /**
     * @param double $pointAmount
     * @return $this
     */
    public function setPoints($pointAmount)
    {
        $this->pointAmount = $pointAmount;

        return $this;
    }

    /**
     * @return bool
     */
    public function canAddPoints()
    {
        if (!$this->getPoints()) {
            return false;
        }

        return true;
    }

    /**
     * @return bool
     */
    public function getIsPossibleSendNotification()
    {
        if (!$this->getPoints()) {
            return false;
        }

        return true;
    }

    /**
     * @return string
     */
    public function getEmailTemplateId()
    {
        return $this->helperData->getRuleEmailTemplate($this->getStoreId());
    }

    /**
     * @return null|int
     */
    public function getEntityId()
    {
        return null;
    }

    /**
     * @return int|false|null null - expiration period won't be changed, false - expiration period is infinity
     */
    public function getExpirationPeriod()
    {
        return $this->getExpirationPeriodFromConfig();
    }

    /**
     * @return bool|int
     */
    protected function getExpirationPeriodFromConfig()
    {
        if ($this->helperData->isEnableExpirationDate($this->getWebsiteId())) {
            return $this->helperData->getDefaultExpirationPeriod($this->getWebsiteId());
        }

        return false;
    }

    /**
     * @param \MageWorx\RewardPoints\Api\Data\PointTransactionInterface $pointTransaction
     * @return $this
     */
    public function processAfterTransactionSave($pointTransaction)
    {
        return $this;
    }

    /**
     * @return string|null
     */
    public function getComment()
    {
        $eventData = $this->getEventData();

        if (!empty($eventData['comment'])) {
            return $eventData['comment'];
        }

        return null;
    }
}