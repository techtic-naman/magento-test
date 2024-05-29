<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\RewardPoints\Helper;

class Store extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * Price constructor.
     *
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\App\Helper\Context $context
     */
    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\Helper\Context $context
    ) {
        $this->storeManager   = $storeManager;
        parent::__construct($context);
    }

    /**
     * @param int $website
     * @param null $priorityStoreId
     * @return int|null
     */
    public function getWebsiteStoreId($website, $priorityStoreId = null)
    {
        $websiteStoreIds = $website->getStoreIds();

        if (!$websiteStoreIds) {
            return null;
        }

        $websiteDefaultStoreId =
            $this->storeManager->getGroup(
                $website->getDefaultGroupId()
            )->getDefaultStoreId();

        if ((int)$priorityStoreId && in_array($priorityStoreId, $websiteStoreIds)) {
            $finalStoreId = $priorityStoreId;
        } elseif ($websiteDefaultStoreId) {
            $finalStoreId = $websiteDefaultStoreId;
        } else {
            $finalStoreId = $websiteStoreIds[0];
        }

        return $finalStoreId;
    }
}
