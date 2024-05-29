<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace MageWorx\CountdownTimersBase\Model;

use Magento\CatalogRule\Model\ResourceModel\Rule as RuleResourceModel;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DataObject;
use Magento\Framework\DataObjectFactory;
use Magento\Framework\Event\ManagerInterface as EventManagerInterface;
use Magento\Framework\Serialize\Serializer\Json as SerializerJson;
use Magento\CatalogRule\Model\Rule\Condition\Combine as ConditionCombine;
use Magento\CatalogRule\Model\Rule\Condition\CombineFactory as ConditionCombineFactory;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use MageWorx\CountdownTimersBase\Api\FrontendCountdownTimerResolverInterface;
use MageWorx\CountdownTimersBase\Api\Data\CountdownTimerInterface;
use MageWorx\CountdownTimersBase\Model\ResourceModel\CountdownTimer\Collection;
use MageWorx\CountdownTimersBase\Model\ResourceModel\CountdownTimer\CollectionFactory;
use MageWorx\CountdownTimersBase\Model\CountdownTimerConfigReaderInterface;
use MageWorx\CountdownTimersBase\Model\Source\CountdownTimer\ProductsAssignType as ProductsAssignTypeOptions;
use MageWorx\CountdownTimersBase\Model\Source\CountdownTimer\Status as StatusOptions;
use MageWorx\CountdownTimersBase\Model\Source\CountdownTimer\DisplayMode as DisplayModeOptions;
use MageWorx\CountdownTimersBase\Helper\TimeStamp as HelperTimeStamp;
use Magento\Framework\App\ProductMetadataInterface;
use Magento\Framework\Exception\NoSuchEntityException;

class FrontendCountdownTimerResolver implements FrontendCountdownTimerResolverInterface
{
    /**
     * Countdown Timer collection factory
     *
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var ConditionCombineFactory
     */
    protected $conditionCombineFactory;

    /**
     * @var SerializerJson
     */
    protected $serializerJson;

    /**
     * @var DataObjectFactory
     */
    protected $objectFactory;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var EventManagerInterface
     */
    protected $eventManager;

    /**
     * @var ResourceConnection
     */
    protected $resourceConnection;

    /**
     * @var ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var ProductMetadataInterface
     */
    protected $productMetadata;

    /**
     * @var HelperTimeStamp
     */
    protected $helperTimeStamp;

    /**
     * Countdown Timer config reader
     *
     * @var CountdownTimerConfigReaderInterface
     */
    private $configReader;

    /**
     * @var TimezoneInterface
     */
    private $timezone;

    /**
     * @var RuleResourceModel
     */
    private $ruleResourceModel;

    /**
     * FrontendCountdownTimerResolver constructor.
     *
     * @param CollectionFactory $collectionFactory
     * @param ConditionCombineFactory $conditionCombineFactory
     * @param SerializerJson $serializerJson
     * @param DataObjectFactory $objectFactory
     * @param StoreManagerInterface $storeManager
     * @param EventManagerInterface $eventManager
     * @param ResourceConnection $resourceConnection
     * @param CountdownTimerConfigReaderInterface $configReader
     * @param ProductRepositoryInterface $productRepository
     * @param TimezoneInterface $timezone
     * @param RuleResourceModel $ruleResourceModel
     * @param ProductMetadataInterface $productMetadata
     * @param HelperTimeStamp $helperTimeStamp
     */
    public function __construct(
        CollectionFactory $collectionFactory,
        ConditionCombineFactory $conditionCombineFactory,
        SerializerJson $serializerJson,
        DataObjectFactory $objectFactory,
        StoreManagerInterface $storeManager,
        EventManagerInterface $eventManager,
        ResourceConnection $resourceConnection,
        CountdownTimerConfigReaderInterface $configReader,
        ProductRepositoryInterface $productRepository,
        TimezoneInterface $timezone,
        RuleResourceModel $ruleResourceModel,
        ProductMetadataInterface $productMetadata,
        HelperTimeStamp $helperTimeStamp
    ) {
        $this->collectionFactory       = $collectionFactory;
        $this->conditionCombineFactory = $conditionCombineFactory;
        $this->serializerJson          = $serializerJson;
        $this->objectFactory           = $objectFactory;
        $this->storeManager            = $storeManager;
        $this->eventManager            = $eventManager;
        $this->resourceConnection      = $resourceConnection;
        $this->configReader            = $configReader;
        $this->productRepository       = $productRepository;
        $this->timezone                = $timezone;
        $this->ruleResourceModel       = $ruleResourceModel;
        $this->productMetadata         = $productMetadata;
        $this->helperTimeStamp         = $helperTimeStamp;
    }

    /**
     * @param int $storeId
     * @param int $customerGroupId
     * @param int $productId
     * @return DataObject|null
     * @throws NoSuchEntityException
     */
    public function getCountdownTimer($storeId, $customerGroupId, $productId): ?DataObject
    {
        /** @var Collection $collection */
        $collection = $this->collectionFactory->create();

        $collection
            ->addStoreFilter($storeId)
            ->addCustomerGroupFilter($customerGroupId)
            ->addProductFilter($productId)
            ->addLoyalDateFilter()
            ->addFieldToFilter(CountdownTimerInterface::STATUS, StatusOptions::ENABLE)
            ->setOrder(CountdownTimerInterface::PRIORITY, Collection::SORT_ORDER_ASC);

        $this->eventManager->dispatch(
            'mageworx_countdowntimersbase_before_load_frontend_countdown_timer_collection_data',
            ['collection' => $collection]
        );

        $countdownTimers = $collection->getData();

        if (empty($countdownTimers)) {
            return null;
        }

        foreach ($countdownTimers as $countdownTimerData) {
            $displayMode        = $countdownTimerData[CountdownTimerInterface::DISPLAY_MODE];
            $productsAssignType = $countdownTimerData[CountdownTimerInterface::PRODUCTS_ASSIGN_TYPE];

            if ($displayMode == DisplayModeOptions::SPECIFIC_PRODUCTS
                && $productsAssignType == ProductsAssignTypeOptions::BY_CONDITIONS
            ) {
                if (!$this->validateProductByConditions(
                    $productId,
                    $countdownTimerData[CountdownTimerInterface::CONDITIONS_SERIALIZED]
                )) {
                    continue;
                }
            }

            $endTimeStamp = 0;

            if (!$countdownTimerData[CountdownTimerInterface::USE_DISCOUNT_DATES]) {
                $fromTimeStamp  = strtotime($countdownTimerData[CountdownTimerInterface::START_DATE]);
                $storeTimeStamp = $this->timezone->scopeTimeStamp($storeId);

                // fix date YYYY-MM-DD 00:00:00 to YYYY-MM-DD 23:59:59
                $toTimeStamp = strtotime($countdownTimerData[CountdownTimerInterface::END_DATE]) + 86399;

                if ($storeTimeStamp > $fromTimeStamp && $storeTimeStamp < $toTimeStamp) {
                    $endTimeStamp = $this->helperTimeStamp->getLocalTimeStamp($toTimeStamp);
                }
            } elseif ($countdownTimerData[CountdownTimerInterface::USE_DISCOUNT_DATES] && empty($isChecked)) {
                $endTimeStamp = $this->getEndTimeStampByDiscountDates($storeId, $customerGroupId, $productId);
                $isChecked    = true;
            }

            if ($endTimeStamp) {
                $countdownTimerData['time_stamp'] = $endTimeStamp;

                return $this->objectFactory->create(['data' => $countdownTimerData]);
            }
        }

        return null;
    }

    /**
     * @param int $productId
     * @param string|null $conditionsSerialized
     * @return bool
     */
    protected function validateProductByConditions($productId, $conditionsSerialized): bool
    {
        if (!$conditionsSerialized) {
            return true;
        }

        /** @var ConditionCombine $conditionsObj */
        $conditions = $this->conditionCombineFactory->create();

        $conditions->setPrefix('conditions');
        $conditions->loadArray($this->serializerJson->unserialize($conditionsSerialized));

        return $conditions->validateByEntityId($productId);
    }

    /**
     * @param int $storeId
     * @param int $customerGroupId
     * @param int $productId
     * @return int|null
     * @throws NoSuchEntityException
     */
    protected function getEndTimeStampByDiscountDates($storeId, $customerGroupId, $productId): ?int
    {
        $needCheckCatalogPriceRules = true;

        $product      = $this->productRepository->getById($productId);
        $specialPrice = $product->getSpecialPrice();
        $finalPrice   = $product->getFinalPrice();

        if (!is_null($specialPrice) && $specialPrice == $finalPrice) {
            $fromDate = $product->getSpecialFromDate();
            $toDate   = $product->getSpecialToDate();

            if ($this->timezone->isScopeDateInInterval($storeId, $fromDate, $toDate)) {
                if (is_null($toDate)) {
                    $needCheckCatalogPriceRules = false;
                } else {
                    // fix date YYYY-MM-DD 00:00:00 to YYYY-MM-DD 23:59:59
                    $toTimeStamp = strtotime($toDate) + 86399;

                    return $this->helperTimeStamp->getLocalTimeStamp($toTimeStamp);
                }
            }
        }

        if ($needCheckCatalogPriceRules) {
            $timeStamp = $this->getEndTimeStampByCatalogPriceRules($storeId, $customerGroupId, $productId);
        }

        return !empty($timeStamp) ? $timeStamp : null;
    }

    /**
     * @param int $storeId
     * @param int $customerGroupId
     * @param int $productId
     * @return int|null
     * @throws NoSuchEntityException
     */
    protected function getEndTimeStampByCatalogPriceRules($storeId, $customerGroupId, $productId): ?int
    {
        $date = (int)gmdate('U');

        $rulesData = $this->ruleResourceModel->getRulesFromProduct(
            $date,
            $this->storeManager->getStore($storeId)->getWebsiteId(),
            $customerGroupId,
            $productId
        );

        foreach ($rulesData as $ruleData) {
            if ($ruleData['to_time']) {

                if ($this->productMetadata->getEdition() == 'Enterprise') {
                    $ruleData['to_time'] -= 86399;

                    if ($ruleData['to_time'] <= $date) {
                        continue;
                    }
                }

                return (int)$ruleData['to_time'];
            }
        }

        return null;
    }
}
