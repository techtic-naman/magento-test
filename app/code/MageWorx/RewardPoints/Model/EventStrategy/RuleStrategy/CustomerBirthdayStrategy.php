<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\RewardPoints\Model\EventStrategy\RuleStrategy;

class CustomerBirthdayStrategy extends \MageWorx\RewardPoints\Model\EventStrategy\AbstractRuleStrategy
{
    /**
     * Core model store manager interface
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \MageWorx\RewardPoints\Model\ResourceModel\PointTransaction
     */
    protected $pointTransactionResource;

    /**
     * CustomerBirthdayStrategy constructor.
     *
     * @param \MageWorx\RewardPoints\Model\ResourceModel\Rule $ruleResource
     * @param \MageWorx\RewardPoints\Helper\Data $helperData
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \MageWorx\RewardPoints\Model\ResourceModel\PointTransaction $pointTransactionResource
     * @param array $data
     */
    public function __construct(
        \MageWorx\RewardPoints\Model\ResourceModel\Rule $ruleResource,
        \MageWorx\RewardPoints\Helper\Data $helperData,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \MageWorx\RewardPoints\Model\ResourceModel\PointTransaction $pointTransactionResource,
        array $data = []
    ) {
        $this->storeManager             = $storeManager;
        $this->pointTransactionResource = $pointTransactionResource;
        parent::__construct($ruleResource, $helperData, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function canAddPoints()
    {
        $websiteId = $this->storeManager->getStore($this->getEntity()->getStoreId())->getWebsiteId();

        $isExistsTransaction = $this->pointTransactionResource->isExistTransaction(
            $this->getEventCode(),
            $this->getEntity()->getId(),
            $websiteId,
            $this->getEntityId()
        );

        if ($isExistsTransaction) {
            return false;
        }

        return parent::canAddPoints();
    }

    /**
     * {@inheritdoc}
     */
    public function getEntityId()
    {
        return $this->getCurrentYear();
    }

    /**
     * {@inheritdoc}
     */
    public function getMessage(array $eventData, $comment = null)
    {
        if (!empty($eventData['customer_dob'])) {
            return __("Customer's Birthday present (%1)", $eventData['customer_dob']);
        }

        return '';
    }

    /**
     * {@inheritdoc}
     */
    public function getEventData()
    {

        $eventData = parent::getEventData();

        /** @var \Magento\Customer\Api\Data\CustomerInterface $entity */
        $entity = $this->getEntity();

        if ($entity) {

            if ($email = $entity->getEmail()) {
                $eventData['customer_email'] = $email;
            }

            if ($dob = $entity->getDob()) {
                $eventData['customer_dob'] = $dob;
            }
        }

        return $eventData;
    }

    /**
     * @return string
     */
    protected function getCurrentYear()
    {
        $now = (new \DateTime())->format(\Magento\Framework\Stdlib\DateTime::DATE_PHP_FORMAT);
        list($year) = explode('-', $now);

        return $year;
    }
}