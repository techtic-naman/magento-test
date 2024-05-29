<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\RewardPoints\Model\EventStrategy\RuleStrategy;


class NewsletterSubscriptionStrategy extends \MageWorx\RewardPoints\Model\EventStrategy\AbstractRuleStrategy
{
    /**
     * @var \Magento\Newsletter\Model\ResourceModel\Subscriber\CollectionFactory
     */
    protected $subscribersCollectionFactory;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \MageWorx\RewardPoints\Model\ResourceModel\PointTransaction
     */
    protected $pointTransactionResource;

    /**
     * NewsletterSubscriptionStrategy constructor.
     *
     * @param \MageWorx\RewardPoints\Model\ResourceModel\Rule $ruleResource
     * @param \MageWorx\RewardPoints\Helper\Data $helperData
     * @param \Magento\Newsletter\Model\ResourceModel\Subscriber\CollectionFactory $subscribersFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \MageWorx\RewardPoints\Model\ResourceModel\PointTransaction $pointTransactionResource
     * @param array $data
     */
    public function __construct(
        \MageWorx\RewardPoints\Model\ResourceModel\Rule $ruleResource,
        \MageWorx\RewardPoints\Helper\Data $helperData,
        \Magento\Newsletter\Model\ResourceModel\Subscriber\CollectionFactory $subscribersFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \MageWorx\RewardPoints\Model\ResourceModel\PointTransaction $pointTransactionResource,
        array $data = []
    ) {
        $this->subscribersCollectionFactory = $subscribersFactory;
        $this->storeManager                 = $storeManager;
        $this->pointTransactionResource     = $pointTransactionResource;
        parent::__construct($ruleResource, $helperData, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function canAddPoints()
    {
        $subscriber = $this->getEntity();

        $subscriberStatuses = [
            \Magento\Newsletter\Model\Subscriber::STATUS_SUBSCRIBED,
            \Magento\Newsletter\Model\Subscriber::STATUS_UNCONFIRMED,
        ];
        if (!in_array($subscriber->getData('subscriber_status'), $subscriberStatuses)) {
            return false;
        }

        $websiteId = $this->storeManager->getStore($this->getEntity()->getStoreId())->getWebsiteId();

        $isExistsTransaction = $this->pointTransactionResource->isExistTransaction(
            $this->getEventCode(),
            $this->getEntity()->getCustomerId(),
            $websiteId
        );

        if ($isExistsTransaction) {
            return false;
        }

        return parent::canAddPoints();
    }

    /**
     * {@inheritdoc}
     */
    public function getMessage(array $eventData, $comment = null)
    {
        if (!empty($eventData['customer_email'])) {
            return __('Signed up for a newsletter with an email %1', $eventData['customer_email']);
        }

        return '';
    }

    /**
     * {@inheritdoc}
     */
    public function getEventData()
    {
        $eventData = parent::getEventData();

        $entity = $this->getEntity();
        if ($entity && $email = $this->getEntity()->getEmail()) {
            $eventData['customer_email'] = $email;
        }

        return $eventData;
    }

    /**
     * {@inheritdoc}
     */
    public function getEntityId()
    {
        return $this->getEntity()->getId();
    }
}