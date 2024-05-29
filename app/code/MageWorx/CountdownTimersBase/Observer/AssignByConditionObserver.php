<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace MageWorx\CountdownTimersBase\Observer;

use Magento\CatalogRule\Model\Rule as CatalogRule;
use Magento\CatalogRule\Model\RuleFactory as CatalogRuleFactory;
use Magento\Framework\Event\Observer;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;
use MageWorx\CountdownTimersBase\Api\Data\CountdownTimerInterface;
use MageWorx\CountdownTimersBase\Model\ResourceModel\CountdownTimer;
use MageWorx\CountdownTimersBase\Model\ResourceModel\CountdownTimer\CollectionFactory;
use MageWorx\CountdownTimersBase\Model\ResourceModel\CountdownTimer\Collection;
use Magento\Framework\Event\ObserverInterface;
use MageWorx\CountdownTimersBase\Model\Source\CountdownTimer\DisplayMode as DisplayModeOptions;
use MageWorx\CountdownTimersBase\Model\Source\CountdownTimer\ProductsAssignType as ProductsAssignTypeOptions;
use MageWorx\CountdownTimersBase\Model\Source\CountdownTimer\Status as StatusOptions;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Observer class
 */
class AssignByConditionObserver implements ObserverInterface
{
    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var CatalogRuleFactory
     */
    protected $catalogRuleFactory;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * AssignByConditionObserver constructor.
     *
     * @param CollectionFactory $collectionFactory
     * @param CatalogRuleFactory $catalogRuleFactory
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        CollectionFactory $collectionFactory,
        CatalogRuleFactory $catalogRuleFactory,
        StoreManagerInterface $storeManager
    ) {
        $this->collectionFactory  = $collectionFactory;
        $this->catalogRuleFactory = $catalogRuleFactory;
        $this->storeManager       = $storeManager;
    }

    /**
     * @param Observer $observer
     * @throws NoSuchEntityException
     */
    public function execute(Observer $observer): void
    {
        $collection = $this->collectionFactory->create();
        $collection
            ->addFieldToFilter(CountdownTimerInterface::STATUS, StatusOptions::ENABLE)
            ->addFieldToFilter(CountdownTimerInterface::DISPLAY_MODE, DisplayModeOptions::SPECIFIC_PRODUCTS)
            ->addFieldToFilter(CountdownTimerInterface::PRODUCTS_ASSIGN_TYPE, ProductsAssignTypeOptions::BY_CONDITIONS);

        $collection->addAssociatedStoreViews();

        $associatedProductIds = $this->getAssociatedProductIds($collection);

        if (empty($associatedProductIds)) {
            return;
        }

        $countdownTimerIds = array_keys($associatedProductIds);
        $connection        = $collection->getConnection();
        $table             = $collection->getTable(CountdownTimer::COUNTDOWN_TIMER_PRODUCT_TABLE);

        $connection->delete($table, [CountdownTimerInterface::COUNTDOWN_TIMER_ID . ' IN (?)' => $countdownTimerIds]);

        foreach ($countdownTimerIds as $timerId) {
            $data = [];

            foreach ($associatedProductIds[$timerId] as $productId) {
                $data[] = [
                    CountdownTimerInterface::COUNTDOWN_TIMER_ID => $timerId,
                    'product_id'                                => $productId
                ];
            }

            if (!empty($data)) {
                $connection->insertMultiple($table, $data);
            }
        }
    }

    /**
     * @param Collection $collection
     * @return array
     * @throws NoSuchEntityException
     */
    protected function getAssociatedProductIds($collection): array
    {
        $associatedProductIds = [];

        foreach ($collection as $countdownTimer) {
            $conditionsSerialized = $countdownTimer->getConditionsSerialized();

            if (!$conditionsSerialized) {
                continue;
            }

            /** @var CatalogRule $catalogRule */
            $catalogRule = $this->catalogRuleFactory->create();
            $catalogRule
                ->setWebsiteIds($this->getWebsiteIds($countdownTimer))
                ->setConditionsSerialized($conditionsSerialized);

            $associatedProductIds[$countdownTimer->getId()] = array_keys($catalogRule->getMatchingProductIds());
        }

        return $associatedProductIds;
    }

    /**
     * @param CountdownTimerInterface $countdownTimer
     * @return string
     * @throws NoSuchEntityException
     */
    protected function getWebsiteIds($countdownTimer): string
    {
        $storeIds = $countdownTimer->getStoreIds();

        if (in_array(Store::DEFAULT_STORE_ID, $storeIds)) {
            return (string)current(array_keys($this->storeManager->getWebsites()));
        }

        $websiteIds = [];

        foreach ($storeIds as $storeId) {
            $websiteIds[] = $this->storeManager->getStore($storeId)->getWebsiteId();
        }

        return (string)current(array_unique($websiteIds));
    }
}
