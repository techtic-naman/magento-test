<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\RewardPoints\Model\EventStrategy\RuleStrategy;

class CustomerReviewStrategy extends \MageWorx\RewardPoints\Model\EventStrategy\AbstractRuleStrategy
{
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \MageWorx\RewardPoints\Model\ResourceModel\PointTransaction
     */
    protected $pointTransactionResource;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product
     */
    protected $productResource;

    /**
     * CustomerReviewStrategy constructor.
     *
     * @param \MageWorx\RewardPoints\Model\ResourceModel\Rule $ruleResource
     * @param \MageWorx\RewardPoints\Helper\Data $helperData
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \MageWorx\RewardPoints\Model\ResourceModel\PointTransaction $pointTransactionResource
     * @param \Magento\Catalog\Model\ResourceModel\Product $productResource
     * @param array $data
     */
    public function __construct(
        \MageWorx\RewardPoints\Model\ResourceModel\Rule $ruleResource,
        \MageWorx\RewardPoints\Helper\Data $helperData,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \MageWorx\RewardPoints\Model\ResourceModel\PointTransaction $pointTransactionResource,
        \Magento\Catalog\Model\ResourceModel\Product $productResource,
        array $data = []
    ) {
        $this->storeManager             = $storeManager;
        $this->pointTransactionResource = $pointTransactionResource;
        $this->productResource          = $productResource;
        parent::__construct($ruleResource, $helperData, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function getMessage(array $eventData, $comment = null)
    {
        if (!empty($eventData['product_name'])) {
            return __('For submitting a review for the product: %1', $eventData['product_name']);
        }

        return __('For submitting of the review');
    }

    /**
     * {@inheritdoc}
     */
    public function getEntityId()
    {
        return $this->getEntity()->getId();
    }

    /**
     * @return bool
     */
    public function canAddPoints()
    {
        /** @var \Magento\Review\Model\Review $review */
        $review = $this->getEntity();

        $isExistTransaction = $this->pointTransactionResource->isExistTransaction(
            $this->getEventCode(),
            $review->getCustomerId(),
            $this->storeManager->getStore($review->getStoreId())->getWebsiteId(),
            $review->getId()
        );

        if ($isExistTransaction) {
            return false;
        }

        return parent::canAddPoints();
    }

    /**
     * {@inheritdoc}
     */
    public function getEventData()
    {

        $eventData = parent::getEventData();

        /** @var \Magento\Review\Model\Review $review */
        $review = $this->getEntity();

        if ($review) {

            $productId = $review->getProductId();
            $storeId   = $review->getStoreId();

            $productName = $this->productResource->getAttributeRawValue($productId, 'name', $storeId);

            if ($productName) {
                $eventData['product_name'] = $productName . ' (#' . $productId . ')';
            }
        }

        return $eventData;
    }
}