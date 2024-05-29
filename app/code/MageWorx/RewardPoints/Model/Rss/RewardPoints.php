<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\RewardPoints\Model\Rss;

class RewardPoints
{
    /**
     * @var \MageWorx\RewardPoints\Model\ResourceModel\Rule\CollectionFactory
     */
    protected $collectionFactory;

    /**
     * RewardPoints constructor.
     *
     * @param \MageWorx\RewardPoints\Model\ResourceModel\Rule\CollectionFactory $collectionFactory
     */
    public function __construct(
        \MageWorx\RewardPoints\Model\ResourceModel\Rule\CollectionFactory $collectionFactory
    ) {
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * @param int $websiteId
     * @param int $customerGroupId
     * @return \MageWorx\RewardPoints\Model\ResourceModel\Rule\Collection
     */
    public function getDiscountCollection($websiteId, $customerGroupId)
    {
        /** @var $collection \MageWorx\RewardPoints\Model\ResourceModel\Rule\Collection */
        $collection = $this->collectionFactory->create();

        return $collection->addWebsiteGroupDateFilter(
            $websiteId,
            $customerGroupId,
            (new \DateTime())->format(\Magento\Framework\Stdlib\DateTime::DATETIME_PHP_FORMAT)
        )
                          ->addFieldToFilter('is_rss', 1)
                          ->setOrder('from_date', 'desc');
    }
}
