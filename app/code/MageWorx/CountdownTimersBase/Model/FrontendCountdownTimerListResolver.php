<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace MageWorx\CountdownTimersBase\Model;

use Magento\Catalog\Model\Product as ProductModel;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;
use Magento\Catalog\Model\ResourceModel\Product\Collection as ProductCollection;
use Magento\Framework\App\ProductMetadataInterface;
use Magento\Framework\Event\ManagerInterface as EventManagerInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Store\Model\StoreManagerInterface;
use MageWorx\CountdownTimersBase\Api\Data\CountdownTimerInterface;
use MageWorx\CountdownTimersBase\Api\FrontendCountdownTimerListResolverInterface;
use MageWorx\CountdownTimersBase\Model\ResourceModel\CountdownTimer\Collection;
use MageWorx\CountdownTimersBase\Model\ResourceModel\CountdownTimer\CollectionFactory;
use MageWorx\CountdownTimersBase\Model\Source\CountdownTimer\Status as StatusOptions;
use MageWorx\CountdownTimersBase\Helper\TimeStamp as HelperTimeStamp;
use Magento\CatalogRule\Model\ResourceModel\Rule as RuleResourceModel;

class FrontendCountdownTimerListResolver implements FrontendCountdownTimerListResolverInterface
{
    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var EventManagerInterface
     */
    protected $eventManager;

    /**
     * @var int
     */
    protected $storeId;

    /**
     * @var int
     */
    protected $customerGroupId;

    /**
     * @var array
     */
    protected $productIds = [];

    /**
     * @var array
     */
    protected $endTimeStampsByTimerDates = [];

    /**
     * @var array
     */
    protected $endTimeStampsByDiscountDates = [];

    /**
     * @var array|null
     */
    protected $endTimeStampsByCatalogPriceRules;

    /**
     * @var HelperTimeStamp
     */
    protected $helperTimeStamp;

    /**
     * @var ProductCollectionFactory
     */
    protected $productCollectionFactory;

    /**
     * @var ProductCollection
     */
    protected $loadedProductCollection;

    /**
     * @var ProductMetadataInterface
     */
    protected $productMetadata;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var TimezoneInterface
     */
    private $timezone;

    /**
     * @var RuleResourceModel
     */
    private $ruleResourceModel;

    /**
     * FrontendCountdownTimerListResolver constructor.
     *
     * @param CollectionFactory $collectionFactory
     * @param TimezoneInterface $timezone
     * @param EventManagerInterface $eventManager
     * @param HelperTimeStamp $helperTimeStamp
     * @param ProductCollectionFactory $productCollectionFactory
     * @param RuleResourceModel $ruleResourceModel
     * @param ProductMetadataInterface $productMetadata
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        CollectionFactory $collectionFactory,
        TimezoneInterface $timezone,
        EventManagerInterface $eventManager,
        HelperTimeStamp $helperTimeStamp,
        ProductCollectionFactory $productCollectionFactory,
        RuleResourceModel $ruleResourceModel,
        ProductMetadataInterface $productMetadata,
        StoreManagerInterface $storeManager
    ) {
        $this->collectionFactory        = $collectionFactory;
        $this->timezone                 = $timezone;
        $this->eventManager             = $eventManager;
        $this->helperTimeStamp          = $helperTimeStamp;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->ruleResourceModel        = $ruleResourceModel;
        $this->productMetadata          = $productMetadata;
        $this->storeManager             = $storeManager;
    }

    /**
     * @param int $customerGroupId
     * @param array $productIds
     * @return array|null
     * @throws NoSuchEntityException
     */
    public function getCountdownTimers($customerGroupId, array $productIds): array
    {
        $this->storeId         = $this->storeManager->getStore()->getId();
        $this->customerGroupId = $customerGroupId;
        $this->productIds      = $productIds;

        /** @var Collection $collection */
        $collection = $this->collectionFactory->create();
        $collection->getSelect()->columns(
            ['product_id' => $collection::PRODUCT_TABLE_ALIAS . '.' . $collection::PRODUCT_TABLE_COLUMN]
        );
        $collection
            ->addStoreFilter($this->storeId)
            ->addCustomerGroupFilter($this->customerGroupId)
            ->addProductFilter($this->productIds)
            ->addLoyalDateFilter()
            ->addFieldToFilter(CountdownTimerInterface::STATUS, StatusOptions::ENABLE)
            ->addFieldToFilter(CountdownTimerInterface::DISPLAY_ON_CATEGORIES, '1')
            ->setOrder(CountdownTimerInterface::PRIORITY, Collection::SORT_ORDER_ASC);

        $this->eventManager->dispatch(
            'mageworx_countdowntimersbase_before_load_productlist_countdown_timer_collection_data',
            ['collection' => $collection]
        );

        $countdownTimers = $collection->getData();

        if (empty($countdownTimers)) {
            throw new NoSuchEntityException(__('Requested Countdown Timers doesn\'t exist'));
        }

        $arrayResult = [];

        foreach ($countdownTimers as $countdownTimerData) {
            $productId = (int)$countdownTimerData[$collection::PRODUCT_TABLE_COLUMN];

            if ($productId) {
                $this->process($arrayResult, $productId, $countdownTimerData);
            } else {
                foreach ($this->productIds as $id) {
                    $this->process($arrayResult, (int)$id, $countdownTimerData);
                }
            }
        }

        return $arrayResult;
    }

    /**
     * @param array $arrayResult
     * @param int $productId
     * @param array $countdownTimerData
     * @throws NoSuchEntityException
     */
    protected function process(array &$arrayResult, $productId, array $countdownTimerData): void
    {
        if (isset($arrayResult[$productId])) {
            return;
        }

        $this->addTimeStamp($countdownTimerData, $productId);

        if (!empty($countdownTimerData['time_stamp'])) {
            $arrayResult[$productId] = $countdownTimerData;
        }
    }

    /**
     * @param array $countdownTimerData
     * @param int $productId
     * @return void
     * @throws NoSuchEntityException
     */
    protected function addTimeStamp(array &$countdownTimerData, $productId): void
    {
        $endTimeStamp = 0;

        if ($countdownTimerData[CountdownTimerInterface::USE_DISCOUNT_DATES]) {
            $endTimeStamp = $this->getEndTimeStampByDiscountDates($productId);
        } else {
            $endTimeStamp = $this->getEndTimeStampByTimerDates($countdownTimerData);
        }

        if ($endTimeStamp) {
            $countdownTimerData['time_stamp'] = $endTimeStamp;
        }
    }

    /**
     * @param array $countdownTimerData
     * @return int
     */
    protected function getEndTimeStampByTimerDates(array $countdownTimerData): int
    {
        $timerId = $countdownTimerData[CountdownTimerInterface::COUNTDOWN_TIMER_ID];

        if (isset($this->endTimeStampsByTimerDates[$timerId])) {
            return $this->endTimeStampsByTimerDates[$timerId];
        }

        $fromTimeStamp  = strtotime($countdownTimerData[CountdownTimerInterface::START_DATE]);
        $storeTimeStamp = $this->timezone->scopeTimeStamp($this->storeId);

        // fix date YYYY-MM-DD 00:00:00 to YYYY-MM-DD 23:59:59
        $toTimeStamp = strtotime($countdownTimerData[CountdownTimerInterface::END_DATE]) + 86399;

        if ($storeTimeStamp > $fromTimeStamp && $storeTimeStamp < $toTimeStamp) {
            $this->endTimeStampsByTimerDates[$timerId] = $this->helperTimeStamp->getLocalTimeStamp($toTimeStamp);
        } else {
            $this->endTimeStampsByTimerDates[$timerId] = 0;
        }

        return $this->endTimeStampsByTimerDates[$timerId];
    }

    /**
     * @param int $productId
     * @return int
     * @throws NoSuchEntityException
     */
    protected function getEndTimeStampByDiscountDates($productId): int
    {
        if (isset($this->endTimeStampsByDiscountDates[$productId])) {
            return $this->endTimeStampsByDiscountDates[$productId];
        }

        if (empty($this->loadedProductCollection)) {
            $productCollection = $this->productCollectionFactory->create();
            $productCollection
                ->addAttributeToSelect(['special_price', 'special_to_date', 'special_from_date'])
                ->addIdFilter($this->productIds)
                ->setStoreId($this->storeId)
                ->addMinimalPrice()
                ->addFinalPrice()
                ->addTaxPercents();

            $this->loadedProductCollection = $productCollection->load();
        }

        $needCheckCatalogPriceRules = true;

        $product      = $this->loadedProductCollection->getItemById($productId);
        $specialPrice = $product->getSpecialPrice();
        $finalPrice   = $product->getFinalPrice();

        if (!is_null($specialPrice) && $specialPrice == $finalPrice) {
            $fromDate = $product->getSpecialFromDate();
            $toDate   = $product->getSpecialToDate();

            if ($this->timezone->isScopeDateInInterval($this->storeId, $fromDate, $toDate)) {
                $needCheckCatalogPriceRules = false;

                if ($toDate !== null) {
                    // fix date YYYY-MM-DD 00:00:00 to YYYY-MM-DD 23:59:59
                    $toTimeStamp = strtotime($toDate) + 86399;

                    $timeStamp = $this->helperTimeStamp->getLocalTimeStamp($toTimeStamp);
                }
            }
        }

        if ($needCheckCatalogPriceRules) {
            $timeStamp = $this->getEndTimeStampByCatalogPriceRules($productId);
        }

        $this->endTimeStampsByDiscountDates[$productId] = !empty($timeStamp) ? $timeStamp : 0;

        return $this->endTimeStampsByDiscountDates[$productId];
    }

    /**
     * @param int $productId
     * @return int|null
     * @throws NoSuchEntityException
     */
    protected function getEndTimeStampByCatalogPriceRules($productId): ?int
    {
        if (is_null($this->endTimeStampsByCatalogPriceRules)) {
            $this->endTimeStampsByCatalogPriceRules = [];

            $date = (int)gmdate('U');

            foreach ($this->getRulesFromProducts($date) as $ruleData) {
                if (isset($this->endTimeStampsByCatalogPriceRules[$ruleData['product_id']])) {
                    continue;
                }

                if ($ruleData['to_time']) {

                    if ($this->productMetadata->getEdition() == 'Enterprise') {
                        $ruleData['to_time'] -= 86399;

                        if ($ruleData['to_time'] <= $date) {
                            continue;
                        }
                    }

                    $this->endTimeStampsByCatalogPriceRules[$ruleData['product_id']] = (int)$ruleData['to_time'];
                }
            }
        }

        if (isset($this->endTimeStampsByCatalogPriceRules[$productId])) {
            return $this->endTimeStampsByCatalogPriceRules[$productId];
        }

        return null;
    }

    /**
     * @param int $date
     * @return array
     * @throws NoSuchEntityException
     */
    protected function getRulesFromProducts($date): array
    {
        $connection = $this->ruleResourceModel->getConnection();
        $select     = $connection->select();
        $select
            ->from($this->ruleResourceModel->getTable('catalogrule_product'))
            ->where('website_id = ?', $this->storeManager->getStore($this->storeId)->getWebsiteId())
            ->where('customer_group_id = ?', $this->customerGroupId)
            ->where('product_id IN (?)', $this->productIds)
            ->where('from_time = 0 or from_time < ?', $date)
            ->where('to_time = 0 or to_time > ?', $date);

        return $connection->fetchAll($select);
    }
}
