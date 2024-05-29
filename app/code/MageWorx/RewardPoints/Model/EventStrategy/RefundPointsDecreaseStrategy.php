<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\RewardPoints\Model\EventStrategy;

class RefundPointsDecreaseStrategy extends \MageWorx\RewardPoints\Model\AbstractEventStrategy
{
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \MageWorx\RewardPoints\Helper\Price
     */
    protected $helperPrice;

    /**
     * @var \MageWorx\RewardPoints\Api\CustomerBalanceRepositoryInterface
     */
    protected $customerBalanceRepository;

    /**
     * @var \MageWorx\RewardPoints\Model\ResourceModel\PointTransaction\CollectionFactory
     */
    protected $pointTransactionCollectionFactory;

    /**
     * RefundPointsDecreaseStrategy constructor.
     *
     * @param \MageWorx\RewardPoints\Helper\Data $helperData
     * @param \MageWorx\RewardPoints\Helper\Price $helperPrice
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \MageWorx\RewardPoints\Model\ResourceModel\PointTransaction\CollectionFactory $pointTransactionCollectionFactory
     * @param \MageWorx\RewardPoints\Api\CustomerBalanceRepositoryInterface $customerBalanceRepository
     * @param array $data
     */
    public function __construct(
        \MageWorx\RewardPoints\Helper\Data $helperData,
        \MageWorx\RewardPoints\Helper\Price $helperPrice,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \MageWorx\RewardPoints\Model\ResourceModel\PointTransaction\CollectionFactory $pointTransactionCollectionFactory,
        \MageWorx\RewardPoints\Api\CustomerBalanceRepositoryInterface $customerBalanceRepository,
        array $data = []
    ) {
        $this->helperPrice                       = $helperPrice;
        $this->storeManager                      = $storeManager;
        $this->pointTransactionCollectionFactory = $pointTransactionCollectionFactory;
        $this->customerBalanceRepository         = $customerBalanceRepository;
        parent::__construct($helperData, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function getMessage(array $eventData, $comment = null)
    {
        $message = '';

        if (!empty($eventData['increment_id'])) {
            $message = __('Points were withdrawn by the refund order %1', $eventData['increment_id']);
        }

        return $message;
    }

    /**
     * {@inheritdoc}
     */
    public function getEventData()
    {
        $eventData['increment_id'] = $this->getEntity()->getIncrementId();

        return $eventData;
    }

    /**
     * {@inheritdoc}
     */
    public function getPoints()
    {
        $customerBalancePoints = $this->getCustomerBalancePoints();

        if (!$customerBalancePoints) {
            return null;
        }

        $earnedByOrderPoints = $this->getEarnedByOrderPoints();

        if (!$earnedByOrderPoints) {
            return null;
        }

        $points = min($customerBalancePoints, $earnedByOrderPoints);

        return -1 * $points;
    }

    /**
     * @return double
     */
    protected function getEarnedByOrderPoints()
    {
        /** @var \Magento\Sales\Model\Order $order */
        $order = $this->getEntity();

        /** @var \MageWorx\RewardPoints\Model\ResourceModel\PointTransaction\Collection $pointTransactionCollection */
        $pointTransactionCollection = $this->pointTransactionCollectionFactory->create();

        $pointTransactionCollection
            ->addCustomerFilter($order->getCustomerId())
            ->addWebsiteFilter($this->storeManager->getStore($order->getStoreId())->getWebsiteId())
            ->addEventCodeFilter(\MageWorx\RewardPoints\Model\Rule::ORDER_PLACED_EARN_EVENT);

        $returnPointsAmount = 0;

        /** @var \MageWorx\RewardPoints\Model\PointTransaction $transaction */
        foreach ($pointTransactionCollection as $transaction) {

            $eventData = $transaction->getEventData();

            if (!empty($eventData['increment_id']) && $eventData['increment_id'] == $order->getIncrementId()) {
                $returnPointsAmount += $transaction->getPointsDelta();
            }
        }

        return $returnPointsAmount;
    }

    /**
     * @return double
     */
    protected function getCustomerBalancePoints()
    {
        /** @var \Magento\Sales\Model\Order $order */
        $order = $this->getEntity();

        $customerBalance = $this->customerBalanceRepository->getByCustomer(
            $order->getCustomerId(),
            $this->storeManager->getStore($order->getStoreId())->getWebsiteId()
        );

        return $customerBalance->getPoints();
    }

    /**
     * {@inheritdoc}
     */
    public function processAfterTransactionSave($pointTransaction)
    {
        if ($pointTransaction->getId() && $pointTransaction->getPointsDelta()) {

            /** @var \Magento\Sales\Model\Order $order */
            $order = $this->getEntity();

            $order->addStatusHistoryComment(
                __(
                    '%1 were withdrawn from the customer account when refunding the order',
                    $this->helperPrice->getFormattedPoints($pointTransaction->getPointsDelta())
                )
            );

            $order->save();
        }

        return parent::processAfterTransactionSave($pointTransaction);
    }

    /**
     * {@inheritdoc}
     */
    public function getIsPossibleSendNotification()
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function getExpirationPeriod()
    {
        return null;
    }
}