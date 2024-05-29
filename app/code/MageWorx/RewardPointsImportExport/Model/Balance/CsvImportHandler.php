<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\RewardPointsImportExport\Model\Balance;

use Magento\Customer\Model\Config\Share;

class CsvImportHandler
{

    const ACTION_REPLACE = 'replace';
    const ACTION_ADD     = 'add';
    const ACTION_DEDUCT  = 'deduct';

    const EXPIRATION_PERIOD_UNCHANGED = 'unchanged';
    const EXPIRATION_PERIOD_UNLIMITED = 'unlimited';
    const EXPIRATION_PERIOD_DEFAULT   = 'default';

    const IMPORT_EVENT = 'import';

    /**
     * Collection of publicly available stores
     *
     * @var \Magento\Store\Model\ResourceModel\Store\Collection
     */
    protected $publicStores;

    /**
     * CSV Processor
     *
     * @var \Magento\Framework\File\Csv
     */
    protected $csvProcessor;

    /**
     * @var \MageWorx\RewardPoints\Model\ResourceModel\PointTransaction\CollectionFactory
     */
    protected $pointTransactionCollectionFactory;

    /**
     * @var \MageWorx\RewardPoints\Model\ResourceModel\CustomerBalance\CollectionFactory
     */
    protected $customerBalanceCollectionFactory;

    /**
     * @var \MageWorx\RewardPoints\Model\ResourceModel\CustomerBalance\Collection
     */
    protected $customerBalanceCollection;

    /**
     * @var \Magento\Framework\Escaper
     */
    protected $escaper;

    /**
     * @var \MageWorx\RewardPoints\Model\PointTransactionApplier
     */
    protected $pointTransactionApplier;

    /**
     * @var \Magento\Customer\Model\ResourceModel\Customer\Collection
     */
    protected $customerCollection;

    /**
     * @var \Magento\Store\Model\ResourceModel\Website\Collection
     */
    protected $websiteCollection;

    /**
     * @var \MageWorx\RewardPoints\Api\CustomerBalanceRepositoryInterface
     */
    protected $customerBalanceRepository;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Framework\DataObjectFactory
     */
    protected $dataObjectFactory;

    /**
     * @var Share
     */
    private $shareConfig;

    /**
     * @var
     */
    protected $eventCode = 'import';

    /**
     * CsvImportHandler constructor.
     *
     * @param \MageWorx\RewardPoints\Model\PointTransactionApplier $pointTransactionApplier
     * @param \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory $customerCollectionFactory
     * @param \Magento\Store\Model\ResourceModel\Website\CollectionFactory $websiteCollectionFactory
     * @param \MageWorx\RewardPoints\Model\ResourceModel\PointTransaction\CollectionFactory $pointTransactionCollectionFactory
     * @param \MageWorx\RewardPoints\Model\ResourceModel\CustomerBalance\CollectionFactory $customerBalanceCollecitonFactory
     * @param \MageWorx\RewardPoints\Api\CustomerBalanceRepositoryInterface $customerBalanceRepository
     * @param \Magento\Framework\Escaper $escaper
     * @param \Magento\Store\Model\ResourceModel\Store\Collection $storeCollection
     * @param \Magento\Directory\Model\CountryFactory $countryFactory
     * @param \Magento\Framework\File\Csv $csvProcessor
     * @param \Magento\Framework\DataObjectFactory $dataObjectFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param Share $shareConfig
     */
    public function __construct(
        \MageWorx\RewardPoints\Model\PointTransactionApplier $pointTransactionApplier,
        \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory $customerCollectionFactory,
        \Magento\Store\Model\ResourceModel\Website\CollectionFactory $websiteCollectionFactory,
        \MageWorx\RewardPoints\Model\ResourceModel\PointTransaction\CollectionFactory $pointTransactionCollectionFactory,
        \MageWorx\RewardPoints\Model\ResourceModel\CustomerBalance\CollectionFactory $customerBalanceCollecitonFactory,
        \MageWorx\RewardPoints\Api\CustomerBalanceRepositoryInterface $customerBalanceRepository,
        \Magento\Framework\Escaper $escaper,
        \Magento\Store\Model\ResourceModel\Store\Collection $storeCollection,
        \Magento\Directory\Model\CountryFactory $countryFactory,
        \Magento\Framework\File\Csv $csvProcessor,
        \Magento\Framework\DataObjectFactory $dataObjectFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        Share $shareConfig
    ) {
        // prevent admin store from loading
        $this->publicStores                      = $storeCollection->setLoadDefault(false);
        $this->csvProcessor                      = $csvProcessor;
        $this->customerCollection                = $customerCollectionFactory->create();
        $this->websiteCollection                 = $websiteCollectionFactory->create();
        $this->pointTransactionCollectionFactory = $pointTransactionCollectionFactory;
        $this->customerBalanceCollectionFactory  = $customerBalanceCollecitonFactory;
        $this->pointTransactionApplier           = $pointTransactionApplier;
        $this->escaper                           = $escaper;
        $this->dataObjectFactory                 = $dataObjectFactory;
        $this->storeManager                      = $storeManager;
        $this->customerBalanceRepository         = $customerBalanceRepository;
        $this->shareConfig                       = $shareConfig;
    }

    /**
     * @param array $file file info retrieved from $_FILES array
     * @throws \Exception
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function importFromCsvFile($file)
    {
        if (!isset($file['tmp_name'])) {
            throw new \Magento\Framework\Exception\LocalizedException(__('Invalid file upload attempt.'));
        }
        $balanceData = $this->csvProcessor->getData($file['tmp_name']);

        if (count($balanceData) < 2) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('Data for import not found')
            );
        }

        array_shift($balanceData);
        array_walk_recursive($balanceData, [$this, 'trim']);

        if ($this->validateBalanceData($balanceData)) {
            foreach ($balanceData as $rowIndex => $dataRow) {
                $this->importBalance($dataRow);
            }
        }
    }

    /**
     * @param $item
     * @param $key
     */
    protected function trim(&$item, $key)
    {
        $item = trim($item);
    }

    /**
     * @param array $balanceData
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function validateBalanceData(array $balanceData)
    {
        return $this->validateByDataFormat($balanceData) && $this->validateByDataValues($balanceData);
    }

    /**
     * @param array $balanceData
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function validateByDataFormat(array $balanceData)
    {
        $uniqueArray = [];

        foreach ($balanceData as $rowIndex => $dataRow) {

            if ($dataRow[0] === '') {
                throw new \Magento\Framework\Exception\LocalizedException(
                    __('Missed website code in line %1', $rowIndex + 2)
                );
            }

            if (trim($dataRow[1]) === '') {
                throw new \Magento\Framework\Exception\LocalizedException(__('Missed Email in line %1', $rowIndex + 2));
            }

            if (!filter_var($dataRow[1], FILTER_VALIDATE_EMAIL)) {
                throw new \Magento\Framework\Exception\LocalizedException(
                    __('Invalid email format in line %1', $rowIndex + 2)
                );
            }

            if (!(is_numeric($dataRow[2]) && $dataRow[2] >= 0)) {
                throw new \Magento\Framework\Exception\LocalizedException(
                    __('Invalid points format in line %1', $rowIndex + 2)
                );
            }

            if (!in_array(strtolower($dataRow[3]), $this->getValidActions())) {
                throw new \Magento\Framework\Exception\LocalizedException(
                    __(
                        'Invalid action in line %1 (The action must be one of "replace", "add", "deduct")',
                        $rowIndex + 2
                    )
                );
            }


            if (!in_array(strtolower($dataRow[4]), $this->getValidExpirationDate()) && !is_numeric($dataRow[4])) {

                throw new \Magento\Framework\Exception\LocalizedException(
                    __(
                        'Invalid expiration period in line %line. The expiration period must be one of "%default", "%unchanged", "%unlimited" or be numeric.',
                        [
                            'line'      => $rowIndex + 2,
                            'default'   => self::EXPIRATION_PERIOD_DEFAULT,
                            'unchanged' => self::EXPIRATION_PERIOD_UNCHANGED,
                            'unlimited' => self::EXPIRATION_PERIOD_UNLIMITED,
                        ]
                    )
                );
            }

            $uniqueKey = $dataRow[0] . '!mw!' . $dataRow[1];

            if (\array_key_exists($uniqueKey, $uniqueArray)) {
                throw new \Magento\Framework\Exception\LocalizedException(
                    __('Duplicate row (email and website code combination) was found in line %1', $rowIndex + 2)
                );
            }

            $uniqueArray[$uniqueKey] = $dataRow;
        }

        return true;
    }

    /**
     * @param array $balanceData
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function validateByDataValues(array $balanceData)
    {
        //Websites
        $requestedWebsiteCodes = array_column($balanceData, '0');
        $requestedWebsiteCodes = array_unique($requestedWebsiteCodes);

        $websiteCodes       = $this->websiteCollection->getColumnValues('code');
        $missedWebsiteCodes = array_diff($requestedWebsiteCodes, $websiteCodes);

        if ($missedWebsiteCodes) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __(
                    'The requested website code(s) is not found: %1.',
                    $this->escaper->escapeHtml(implode(' ,', $missedWebsiteCodes))
                )
            );
        }

        //Customers
        $requestedCustomerEmails = array_column($balanceData, '1');
        $requestedCustomerEmails = array_unique($requestedCustomerEmails);

        $this->customerCollection->addFieldToFilter('email', ['in' => $requestedCustomerEmails]);
        $customerEmails = $this->customerCollection->getColumnValues('email');

        $missedEmails = array_diff($requestedCustomerEmails, $customerEmails);

        if ($missedEmails) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __(
                    'The requested customer email(s) not found: %1.',
                    $this->escaper->escapeHtml(implode(' ,', $missedEmails))
                )
            );
        }

        $missedEmailsWebsiteCodesRelation = [];
        foreach ($balanceData as $data) {
            // data with index 0 must represent website code
            $websiteCode = $data[0];

            // data with index 1 must represent customer email
            $customerEmail = $data[1];

            $customer = $this->getCustomerByWebsite($customerEmail, $data);
            if (!$customer) {
                $missedEmailsWebsiteCodesRelation[] = 'email - ' . $customerEmail . ', website code - ' . $websiteCode;
            }
        }

        if (!empty($missedEmailsWebsiteCodesRelation)) {
            $missedEmailsWebsiteCodesRelationAsString = $this->convertMultiArrayToSting(
                $missedEmailsWebsiteCodesRelation
            );
            throw new \Magento\Framework\Exception\LocalizedException(
                __(
                    'Customer with not correct email(s) and website code(s): %1',
                    $missedEmailsWebsiteCodesRelationAsString
                )
            );
        }

        return true;
    }

    /**
     * Check if public store with specified code still exists
     *
     * @param string $storeCode
     * @return boolean
     */
    protected function _isStoreCodeValid($storeCode)
    {
        $isStoreCodeValid = false;
        foreach ($this->publicStores as $store) {
            if ($storeCode === $store->getCode()) {
                $isStoreCodeValid = true;
                break;
            }
        }

        return $isStoreCodeValid;
    }


    /**
     * @return array
     */
    protected function getValidActions()
    {
        return [
            self::ACTION_ADD,
            self::ACTION_DEDUCT,
            self::ACTION_REPLACE
        ];
    }

    /**
     * @return array
     */
    protected function getValidExpirationDate()
    {
        return [
            self::EXPIRATION_PERIOD_UNCHANGED,
            self::EXPIRATION_PERIOD_UNLIMITED,
            self::EXPIRATION_PERIOD_DEFAULT
        ];
    }

    /**
     * @param array $balanceData
     * @return float|int|mixed
     * @throws \Exception
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function importBalance(array $balanceData)
    {
        // data with index 0 must represent website code
        $websiteCode = $balanceData[0];

        /** @var \Magento\Store\Model\Website $website */
        $website   = $this->websiteCollection->getItemByColumnValue('code', $websiteCode);
        $websiteId = $website->getWebsiteId();
        $storeId   = $website->getDefaultStore()->getStoreId();

        // data with index 1 must represent customer email
        $customerEmail = $balanceData[1];

        /** @var \Magento\Customer\Api\Data\CustomerInterface $customer */
        $customer   = $this->getCustomerByWebsite($customerEmail, $balanceData);
        $customerId = $customer->getId();

        // data with index 4 must represent expiration period
        $expirationPeriod = $balanceData[4];

        $data = [
            'customer_id'       => $customerId,
            'store_id'          => $storeId,
            'expiration_period' => $expirationPeriod
        ];

        // data with index 2 must represent points delta
        $pointsDelta = $balanceData[2];

        // data with index 3 must represent action
        $action = $balanceData[3];


        if (strtolower($action) === self::ACTION_ADD) {
            // Empty - we don't modify $pointsDelta for this case
        } elseif (strtolower($action) === self::ACTION_DEDUCT) {

            $pointsDelta = (-1) * $pointsDelta;

            $customerBalance = $this->customerBalanceRepository->getByCustomer($customerId, $websiteId);

            //Recalculate points delta
            if ($customerBalance && $customerBalance->getPoints() > 0) {

                if ($customerBalance->getPoints() + $pointsDelta < 0) {
                    $pointsDelta = (-1) * $customerBalance->getPoints();
                }
            } else {
                $pointsDelta = 0;
            }

        } else {
            $customerBalance = $this->customerBalanceRepository->getByCustomer($customerId, $websiteId);

            //Reset customer balance
            if ($customerBalance && $customerBalance->getPoints() > 0) {

                $resetData = $data;

                $resetData['points_delta'] = -$customerBalance->getPoints();

                if ($pointsDelta) {
                    $resetData['reset_comment'] = __('Reset balance for import of the new one.');
                } else {
                    $resetData['reset_comment'] = __('Reset balance.');
                }

                $resetPointBalanceObject = $this->dataObjectFactory->create();
                $resetPointBalanceObject->setData($resetData);

                $this->pointTransactionApplier->applyTransaction(
                    self::IMPORT_EVENT,
                    $customer,
                    $resetData['store_id'],
                    $resetPointBalanceObject
                );
            }
        }

        if ($pointsDelta != 0) {

            $data['points_delta'] = $pointsDelta;

            // data with index 5 must represent comment
            if (!empty($balanceData[5])) {
                $data['comment'] = $balanceData[5];
            }

            $updatePointBalanceObject = $this->dataObjectFactory->create();
            $updatePointBalanceObject->setData($data);

            $this->pointTransactionApplier->applyTransaction(
                self::IMPORT_EVENT,
                $customer,
                $data['store_id'],
                $updatePointBalanceObject
            );
        }

        return $pointsDelta;
    }

    /**
     * @param string $customerEmail
     * @param array $balanceData
     * @return mixed
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function getCustomerByWebsite($customerEmail, $balanceData)
    {
        if ($this->shareConfig->isGlobalScope()) {
            return $this->customerCollection->getItemByColumnValue('email', $customerEmail);
        }

        $customers          = $this->customerCollection->getItemsByColumnValue('email', $customerEmail);
        $balanceWebsiteCode = $balanceData[0];
        $balanceWebsite     = $this->websiteCollection->getItemByColumnValue('code', $balanceWebsiteCode);
        $balanceWebsiteId   = $balanceWebsite->getWebsiteId();

        foreach ($customers as $customer) {
            if ($customer->getWebsiteId() == $balanceWebsiteId) {
                return $customer;
            }
        }
    }

    /**
     * @param array $missedEmailsWebsiteCodesRelation
     * @return string
     */
    protected function convertMultiArrayToSting($missedEmailsWebsiteCodesRelation)
    {
        $missedStr = '';
        foreach ($missedEmailsWebsiteCodesRelation as $value) {
            $missedStr = $missedStr . $value . '; ';
        }

        return $missedStr;
    }
}