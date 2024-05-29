<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\RewardPoints\Model\Config\Backend;

use MageWorx\RewardPoints\Model\Source\ExpirationPeriodUpdate;
use MageWorx\RewardPoints\Model\ResourceModel\CustomerBalance\CollectionFactory;

class UpdateExpirationDates extends \Magento\Framework\App\Config\Value
{
    /**
     * @var CollectionFactory
     */
    protected $customerBalanceCollectionFactory;

    /**
     * @var \MageWorx\RewardPoints\Api\CustomerBalanceRepositoryInterface
     */
    protected $customerBalanceRepository;

    /**
     * @var \MageWorx\RewardPoints\Helper\ExpirationDate
     */
    protected $helperExpirationDate;

    /**
     * @var \MageWorx\RewardPoints\Helper\Data
     */
    protected $helperData;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Config\Model\ResourceModel\Config\Data\Collection
     */
    protected $configCollectionFactory;

    /**
     * Config paths for actual settings
     *
     * @var array
     */
    protected $actualSettingPaths = [
        \MageWorx\RewardPoints\Helper\Data::ENABLE_EXPIRATION_DATE,
        \MageWorx\RewardPoints\Helper\Data::DEFAULT_EXPIRATION_PERIOD_IN_DAYS,
        \MageWorx\RewardPoints\Helper\Data::UPDATE_DATES_CONDITION,
    ];

    /**
     * UpdateExpirationDates constructor.
     *
     * @param \MageWorx\RewardPoints\Helper\Data $helperData
     * @param \MageWorx\RewardPoints\Helper\ExpirationDate $helperExpirationDate
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Config\Model\ResourceModel\Config\Data\CollectionFactory $configCollectionFactory
     * @param CollectionFactory $customerBalanceCollectionFactory
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $config
     * @param \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb $resourceCollection
     * @param array $data
     */
    public function __construct(
        \MageWorx\RewardPoints\Helper\Data $helperData,
        \MageWorx\RewardPoints\Helper\ExpirationDate $helperExpirationDate,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Config\Model\ResourceModel\Config\Data\CollectionFactory $configCollectionFactory,
        \MageWorx\RewardPoints\Model\ResourceModel\CustomerBalance\CollectionFactory $customerBalanceCollectionFactory,
        \MageWorx\RewardPoints\Api\CustomerBalanceRepositoryInterface $customerBalanceRepository,
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\App\Config\ScopeConfigInterface $config,
        \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection,
        array $data = []
    ) {
        $this->helperData                       = $helperData;
        $this->helperExpirationDate             = $helperExpirationDate;
        $this->storeManager                     = $storeManager;
        $this->configCollectionFactory          = $configCollectionFactory;
        $this->customerBalanceCollectionFactory = $customerBalanceCollectionFactory;
        $this->customerBalanceRepository        = $customerBalanceRepository;
        parent::__construct($context, $registry, $config, $cacheTypeList, $resource, $resourceCollection, $data);
    }


    /**
     * {@inheritdoc}
     */
    public function beforeSave()
    {
        if ($this->isValueChanged() && $this->getValue() && $this->getOldValue()) {
            $this->proccess($this->getValue());
        }
        return parent::beforeSave();
    }


    /**
     * {@inheritdoc}
     */
    public function beforeDelete()
    {
        if ($this->isValueChanged() && $this->getScope() == \Magento\Config\Block\System\Config\Form::SCOPE_WEBSITES) {

            $configCollection = $this->configCollectionFactory->create();
            $configCollection
                ->addFieldToFilter('scope', \Magento\Config\Block\System\Config\Form::SCOPE_DEFAULT)
                ->addFieldToFilter('scope_id', 0)
                ->addFieldToFilter('path', $this->getPath());

            if ($configCollection->count()) {
                $this->proccess($configCollection->getFirstItem()->getValue());
            }
        }

        parent::beforeDelete();
    }

    /**
     * @param string $value
     */
    protected function proccess($value)
    {
        $sectionPath = $this->getSectionPath();

        if ($this->getScope() == \Magento\Config\Block\System\Config\Form::SCOPE_WEBSITES) {

            $currentScopeData = $this->getCurrentScopeData($sectionPath);
            $scopeData = $this->composeScopeDataFromWebsitesScope($currentScopeData, $sectionPath);

            if ($scopeData) {

                if (!$scopeData[\MageWorx\RewardPoints\Helper\Data::ENABLE_EXPIRATION_DATE]) {
                    return;
                }

                $this->modifyExpirationDates($scopeData, $this->getScopeId(), $value);
            }

        } elseif ($this->getScope() == \Magento\Config\Block\System\Config\Form::SCOPE_DEFAULT) {

            foreach ($this->storeManager->getWebsites() as $website) {

                $currentScopeData = $this->getCurrentScopeData($sectionPath);

                $scopeData = $this->composeScopeDataFromDefaultScope($currentScopeData, $sectionPath, $website);

                if ($scopeData) {

                    if (!$scopeData[\MageWorx\RewardPoints\Helper\Data::ENABLE_EXPIRATION_DATE]) {
                        return;
                    }

                    $this->modifyExpirationDates($scopeData, $website->getId(), $value);
                }
            }
        }
    }

    /**
     * @param array $currentScopeData
     * @param string $sectionPath
     * @param \Magento\Store\Model\Website $website
     * @return array|bool
     */
    protected function composeScopeDataFromDefaultScope(
        array $currentScopeData,
        $sectionPath,
        \Magento\Store\Model\Website $website
    ) {
        $scopeData = $currentScopeData;

        $configCollection = $this->configCollectionFactory->create();
        $configCollection->addScopeFilter(
            \Magento\Config\Block\System\Config\Form::SCOPE_WEBSITES,
            $website->getId(),
            $sectionPath
        );

        foreach ($this->actualSettingPaths as $path) {

            foreach ($configCollection as $item) {

                if ($item->getPath() == $path) {
                    //Current setting has own value for website scope
                    if ($item->getPath() == $this->getPath()) {
                        return false;
                    }
                    $scopeData[$path] = $item->getValue();
                }
            }
        }

        return $scopeData;
    }

    /**
     * @param string $sectionPath
     * @return array|false
     */
    protected function composeScopeDataFromWebsitesScope($currentScopeData, $sectionPath)
    {
        $scopeData = [];

        $configCollection = $this->configCollectionFactory->create();
        $configCollection->addScopeFilter(
            \Magento\Config\Block\System\Config\Form::SCOPE_DEFAULT,
            0,
            $sectionPath
        );

        foreach ($configCollection as $item) {

            if (!in_array($item->getPath(), $this->actualSettingPaths)) {
                continue;
            }

            if (empty($currentScopeData[$item->getPath()])) {
                $scopeData[$item->getPath()] = $item->getValue();
            }
        }

        return $scopeData;
    }

    /**
     * @return array
     */
    protected function getCurrentScopeData($sectionPath)
    {
        $scopeData = [];

        foreach ($this->_getData('fieldset_data') as $settingStringId => $settingValue) {
            $scopeData[$sectionPath . '/' . $settingStringId] = $settingValue;
        }

        return $scopeData;
    }

    /**
     * @return string
     */
    protected function getSectionPath()
    {
        $pathParts   = explode('/', $this->getPath());
        array_pop($pathParts);
        return implode('/', $pathParts);
    }

    /**
     * Retrieve count of modified point balances
     *
     * @param array $scopeData
     * @param int $websiteId
     * @return int
     */
    protected function modifyExpirationDates(array $scopeData, $websiteId, $value)
    {
        if (!$scopeData[\MageWorx\RewardPoints\Helper\Data::ENABLE_EXPIRATION_DATE]) {
            return 0;
        }

        if ($scopeData[\MageWorx\RewardPoints\Helper\Data::UPDATE_DATES_CONDITION] == ExpirationPeriodUpdate::UPDATE_NOTHING) {
            return 0;
        }

        $diff = $value - $this->getOldValue();

        /** @var \MageWorx\RewardPoints\Model\ResourceModel\CustomerBalance\Collection $collection */
        $collection = $this->customerBalanceCollectionFactory->create();
        $collection->addPositivePointBalanceFilter();
        $collection->addWebsiteFilter($websiteId);

        if ($scopeData[\MageWorx\RewardPoints\Helper\Data::UPDATE_DATES_CONDITION] == ExpirationPeriodUpdate::UPDATE_NOT_NULL_BALANCES) {
            $collection->addNotNullExpirationDateFilter();
        }

        $count = 0;

        /** @var \MageWorx\RewardPoints\Api\Data\CustomerBalanceInterface $customerBalance */
        foreach ($collection as $customerBalance) {

            $expirationDate = $this->helperExpirationDate->getExpirationDateForBalance($customerBalance, $diff);
            $customerBalance->setExpirationDate($expirationDate);
            $this->customerBalanceRepository->save($customerBalance);
            $count++;
        }

        return $count;
    }
}