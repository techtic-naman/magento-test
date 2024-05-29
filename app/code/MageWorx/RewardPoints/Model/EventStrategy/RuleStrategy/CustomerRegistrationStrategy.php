<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace MageWorx\RewardPoints\Model\EventStrategy\RuleStrategy;

class CustomerRegistrationStrategy extends \MageWorx\RewardPoints\Model\EventStrategy\AbstractRuleStrategy
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
     * CustomerRegistrationStrategy constructor.
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
     * @return array
     */
    public function getEventData()
    {
        $eventData = parent::getEventData();

        /** @var \Magento\Customer\Api\Data\CustomerInterface $entity */
        $entity = $this->getEntity();

        if ($entity) {
            $email            = $entity->getEmail();
            $registrationDate = $entity->getCreatedAt();

            if ($email) {
                $eventData['customer_email'] = $email;
            }

            if ($registrationDate) {
                $eventData['customer_registration'] = $registrationDate;
            }
        }

        return $eventData;
    }

    /**
     * {@inheritdoc}
     */
    public function getMessage(array $eventData, $comment = null)
    {
        if (!empty($eventData['customer_email'])) {
            return __('Customer registration with email %1', $eventData['customer_email']);
        }

        return __('Customer registration');
    }

    /**
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function canAddPoints()
    {
        $websiteId = $this->storeManager->getStore($this->getEntity()->getStoreId())->getWebsiteId();

        $isExistsTransaction = $this->pointTransactionResource->isExistTransaction(
            $this->getEventCode(),
            $this->getEntity()->getId(),
            $websiteId
        );

        if ($isExistsTransaction) {
            return false;
        }

        return parent::canAddPoints();
    }
}
