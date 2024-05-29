<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace MageWorx\CountdownTimersBase\Controller\Adminhtml\CountdownTimer;

use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Controller\Result\Redirect as ResultRedirect;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;
use MageWorx\CountdownTimersBase\Api\CountdownTimerRepositoryInterface;
use MageWorx\CountdownTimersBase\Api\Data\CountdownTimerInterfaceFactory;
use MageWorx\CountdownTimersBase\Api\Data\CountdownTimerInterface;
use MageWorx\CountdownTimersBase\Helper\CountdownTimer\Rules as RulesHelper;
use MageWorx\CountdownTimersBase\Model\Source\CountdownTimer\DisplayMode as DisplayModeOptions;
use MageWorx\CountdownTimersBase\Model\Source\CountdownTimer\ProductsAssignType as ProductsAssignTypeOptions;
use Magento\Framework\Serialize\Serializer\Json as SerializerJson;
use Magento\Framework\Exception\NoSuchEntityException;
use Psr\Log\LoggerInterface;
use Magento\Framework\Stdlib\DateTime\Filter\Date as DateFilter;

class Save extends \MageWorx\CountdownTimersBase\Controller\Adminhtml\CountdownTimer
{
    /**
     * @var DataPersistorInterface
     */
    protected $dataPersistor;

    /**
     * @var CountdownTimerInterfaceFactory
     */
    protected $countdownTimerFactory;

    /**
     * @var RulesHelper
     */
    protected $rulesHelper;

    /**
     * @var SerializerJson
     */
    protected $serializerJson;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var DateFilter
     */
    protected $dateFilter;

    /**
     * Save constructor.
     *
     * @param Context $context
     * @param ResultFactory $resultFactory
     * @param LoggerInterface $logger
     * @param CountdownTimerRepositoryInterface $countdownTimerRepository
     * @param DataPersistorInterface $dataPersistor
     * @param CountdownTimerInterfaceFactory $countdownTimerFactory
     * @param RulesHelper $rulesHelper
     * @param SerializerJson $serializerJson
     * @param StoreManagerInterface $storeManager
     * @param DateFilter $dateFilter
     */
    public function __construct(
        Context $context,
        ResultFactory $resultFactory,
        LoggerInterface $logger,
        CountdownTimerRepositoryInterface $countdownTimerRepository,
        DataPersistorInterface $dataPersistor,
        CountdownTimerInterfaceFactory $countdownTimerFactory,
        RulesHelper $rulesHelper,
        SerializerJson $serializerJson,
        StoreManagerInterface $storeManager,
        DateFilter $dateFilter
    ) {
        parent::__construct($context, $resultFactory, $logger, $countdownTimerRepository);

        $this->dataPersistor         = $dataPersistor;
        $this->countdownTimerFactory = $countdownTimerFactory;
        $this->rulesHelper           = $rulesHelper;
        $this->serializerJson        = $serializerJson;
        $this->storeManager          = $storeManager;
        $this->dateFilter            = $dateFilter;
    }

    /**
     * @return ResultRedirect
     */
    public function execute(): ResultRedirect
    {
        /** @var ResultRedirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

        $data = $this->getRequest()->getPostValue();

        if (!empty($data[CountdownTimerInterface::COUNTDOWN_TIMER_ID])) {
            $id = $data[CountdownTimerInterface::COUNTDOWN_TIMER_ID];
        } else {
            $id = null;
        }

        try {
            $data = $this->prepareData($data);

            if ($id) {
                $countdownTimer = $this->countdownTimerRepository->getById((int)$id);
            } else {
                unset($data[CountdownTimerInterface::COUNTDOWN_TIMER_ID]);
                $countdownTimer = $this->countdownTimerFactory->create();
                $countdownTimer->isObjectNew(true);
            }

            $countdownTimer->setData($data);

            $this->countdownTimerRepository->save($countdownTimer);

            $this->messageManager->addSuccessMessage(__('You saved the Countdown Timer.'));
            $this->dataPersistor->clear('mageworx_countdowntimersbase_countdown_timer');

            if ($this->getRequest()->getParam('back')) {
                $resultRedirect->setPath(
                    'mageworx_countdowntimersbase/countdownTimer/edit',
                    [CountdownTimerInterface::COUNTDOWN_TIMER_ID => $countdownTimer->getId()]
                );
            } else {
                $resultRedirect->setPath('mageworx_countdowntimersbase/countdownTimer');
            }
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            $this->logger->critical($e);

            $this->dataPersistor->set('mageworx_countdowntimersbase_countdown_timer', $data);

            if ($id) {
                $resultRedirect->setPath(
                    'mageworx_countdowntimersbase/countdownTimer/edit',
                    [CountdownTimerInterface::COUNTDOWN_TIMER_ID => $id]
                );
            } else {
                $resultRedirect->setPath('mageworx_countdowntimersbase/countdownTimer/new');
            }

        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(__('There was a problem saving the Countdown Timer.'));
            $this->logger->critical($e);

            $this->dataPersistor->set('mageworx_countdowntimersbase_countdown_timer', $data);

            if ($id) {
                $resultRedirect->setPath(
                    'mageworx_countdowntimersbase/countdownTimer/edit',
                    [CountdownTimerInterface::COUNTDOWN_TIMER_ID => $id]
                );
            } else {
                $resultRedirect->setPath('mageworx_countdowntimersbase/countdownTimer/new');
            }
        }

        return $resultRedirect;
    }

    /**
     * @param array $data
     * @return array
     * @throws NoSuchEntityException
     */
    protected function prepareData(array $data): array
    {
        $this->prepareStoreIds($data);
        $this->prepareConditions($data);
        $this->prepareProducts($data);
        $this->prepareDates($data);
        $this->prepareDesign($data);

        return $data;
    }

    /**
     * @param array $data
     */
    protected function prepareStoreIds(array &$data): void
    {
        if (in_array(Store::DEFAULT_STORE_ID, $data[CountdownTimerInterface::STORE_IDS])) {
            $data[CountdownTimerInterface::STORE_IDS] = [0];
        }
    }

    /**
     * @param array $data
     * @throws NoSuchEntityException
     */
    protected function prepareProducts(array &$data): void
    {
        $data[CountdownTimerInterface::PRODUCT_IDS] = [];

        if ($data[CountdownTimerInterface::DISPLAY_MODE] == DisplayModeOptions::SPECIFIC_PRODUCTS
            && $data[CountdownTimerInterface::PRODUCTS_ASSIGN_TYPE] == ProductsAssignTypeOptions::SPECIFIC_PRODUCTS
            && !empty($data['specific-products'])
            && !empty($data['specific-products'][CountdownTimerInterface::PRODUCT_IDS])
        ) {
            $data[CountdownTimerInterface::PRODUCT_IDS] = array_column(
                $data['specific-products'][CountdownTimerInterface::PRODUCT_IDS],
                'id'
            );
        } elseif ($data[CountdownTimerInterface::DISPLAY_MODE] == DisplayModeOptions::SPECIFIC_PRODUCTS
            && $data[CountdownTimerInterface::PRODUCTS_ASSIGN_TYPE] == ProductsAssignTypeOptions::BY_CONDITIONS
            && $data[CountdownTimerInterface::CONDITIONS_SERIALIZED]
        ) {
            $rule = $this->rulesHelper->getRuleModel();
            $rule->setConditionsSerialized($data[CountdownTimerInterface::CONDITIONS_SERIALIZED]);
            $rule->setData('website_ids', $this->getWebsiteIdsFromStoreIds($data[CountdownTimerInterface::STORE_IDS]));

            $productIds = $rule->getMatchingProductIds();

            if (!empty($productIds)) {
                $data[CountdownTimerInterface::PRODUCT_IDS] = array_keys($productIds);
            }
        }

        unset($data['specific-products']);
    }

    /**
     * @param array $data
     */
    protected function prepareConditions(array &$data): void
    {
        $data[CountdownTimerInterface::CONDITIONS_SERIALIZED] = null;

        if ($data[CountdownTimerInterface::DISPLAY_MODE] == DisplayModeOptions::SPECIFIC_PRODUCTS
            && $data[CountdownTimerInterface::PRODUCTS_ASSIGN_TYPE] == ProductsAssignTypeOptions::BY_CONDITIONS
            && !empty($data['rule']['conditions'])
        ) {
            $ruleModel = $this->rulesHelper->getRuleModel();
            $ruleData  = [
                'conditions' => $data['rule']['conditions']
            ];
            $ruleModel->loadPost($ruleData);

            $data[CountdownTimerInterface::CONDITIONS_SERIALIZED] = $this->serializerJson->serialize(
                $ruleModel->getConditions()->asArray()
            );
        }

        unset($data['rule']);
    }

    /**
     * @param array $data
     */
    protected function prepareDates(array &$data): void
    {
        if ($data[CountdownTimerInterface::USE_DISCOUNT_DATES]) {
            $data[CountdownTimerInterface::START_DATE] = null;
            $data[CountdownTimerInterface::END_DATE]   = null;
        } else {
            $dates        = [];

            if (!empty($data[CountdownTimerInterface::START_DATE])) {
                $dates[CountdownTimerInterface::START_DATE]        = $this->dateFilter->filter($data[CountdownTimerInterface::START_DATE]);
            }

            if (!empty($data[CountdownTimerInterface::END_DATE])) {
                $dates[CountdownTimerInterface::END_DATE]        = $this->dateFilter->filter($data[CountdownTimerInterface::END_DATE]);
            }

            $data  = array_merge($data, $dates);
        }
    }

    /**
     * @param array $data
     */
    protected function prepareDesign(array &$data): void
    {
        $data[CountdownTimerInterface::THEME]  = $data['templates'][CountdownTimerInterface::THEME];
        $data[CountdownTimerInterface::ACCENT] = $data['templates'][CountdownTimerInterface::ACCENT];

        unset($data['templates']);
    }

    /**
     * @param array $storeIds
     * @return array
     * @throws NoSuchEntityException
     */
    protected function getWebsiteIdsFromStoreIds(array $storeIds): array
    {
        if (in_array(Store::DEFAULT_STORE_ID, $storeIds)) {
            return array_keys($this->storeManager->getWebsites());
        }

        $websiteIds = [];

        foreach ($storeIds as $storeId) {
            $websiteIds[] = $this->storeManager->getStore($storeId)->getWebsiteId();
        }

        return array_unique($websiteIds);
    }
}
