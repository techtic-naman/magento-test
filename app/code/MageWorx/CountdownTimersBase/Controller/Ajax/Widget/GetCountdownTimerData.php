<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace MageWorx\CountdownTimersBase\Controller\Ajax\Widget;

use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory as ResultJsonFactory;
use Magento\Framework\Controller\Result\Json as ResultJson;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use MageWorx\CountdownTimersBase\Api\Data\CountdownTimerInterface;
use MageWorx\CountdownTimersBase\Helper\TimeStamp as HelperTimeStamp;
use MageWorx\CountdownTimersBase\Model\ResourceModel\CountdownTimer\Collection;
use MageWorx\CountdownTimersBase\Model\ResourceModel\CountdownTimer\CollectionFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\Exception\NoSuchEntityException;
use MageWorx\CountdownTimersBase\Model\Source\CountdownTimer\Status as StatusOptions;
use MageWorx\CountdownTimersBase\Model\Source\CountdownTimer\DisplayMode as DisplayModeOptions;
use Psr\Log\LoggerInterface;

class GetCountdownTimerData extends \Magento\Framework\App\Action\Action
{
    /**
     * @var ResultJsonFactory
     */
    protected $resultJsonFactory;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var CustomerSession
     */
    protected $customerSession;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var HelperTimeStamp
     */
    protected $helperTimeStamp;

    /**
     * @var TimezoneInterface
     */
    private $timezone;

    /**
     * GetCountdownTimerData constructor.
     *
     * @param Context $context
     * @param ResultJsonFactory $resultJsonFactory
     * @param StoreManagerInterface $storeManager
     * @param CustomerSession $customerSession
     * @param LoggerInterface $logger
     * @param CollectionFactory $collectionFactory
     * @param TimezoneInterface $timezone
     * @param HelperTimeStamp $helperTimeStamp
     */
    public function __construct(
        Context $context,
        ResultJsonFactory $resultJsonFactory,
        StoreManagerInterface $storeManager,
        CustomerSession $customerSession,
        LoggerInterface $logger,
        CollectionFactory $collectionFactory,
        TimezoneInterface $timezone,
        HelperTimeStamp $helperTimeStamp
    ) {
        parent::__construct($context);

        $this->resultJsonFactory = $resultJsonFactory;
        $this->storeManager      = $storeManager;
        $this->customerSession   = $customerSession;
        $this->logger            = $logger;
        $this->collectionFactory = $collectionFactory;
        $this->timezone          = $timezone;
        $this->helperTimeStamp   = $helperTimeStamp;
    }

    /**
     * @return ResultJson|null
     */
    public function execute(): ?ResultJson
    {
        if ($this->getRequest()->isAjax()) {
            $result = $this->resultJsonFactory->create();

            try {
                /** @var Collection $collection */
                $collection = $this->collectionFactory->create();

                $collection
                    ->addIdsFilter([(int)$this->getRequest()->getParam('countdownTimerId')])
                    ->addStoreFilter($this->storeManager->getStore()->getId())
                    ->addCustomerGroupFilter($this->customerSession->getCustomerGroupId())
                    ->addFieldToFilter(CountdownTimerInterface::STATUS, StatusOptions::ENABLE)
                    ->addFieldToFilter(CountdownTimerInterface::DISPLAY_MODE, DisplayModeOptions::CUSTOM)
                    ->addDateFilter();

                $this->_eventManager->dispatch(
                    'mageworx_countdowntimersbase_before_load_widget_countdown_timer_collection_data',
                    ['collection' => $collection]
                );

                $countdownTimerData = current($collection->getData());

                if (!$countdownTimerData) {
                    throw new NoSuchEntityException(__('Requested Countdown Timer doesn\'t exist'));
                }

                $endTimeStamp = strtotime($countdownTimerData[CountdownTimerInterface::END_DATE]);

                if ($countdownTimerData[CountdownTimerInterface::END_DATE]) {
                    // fix date YYYY-MM-DD 00:00:00 to YYYY-MM-DD 23:59:59
                    $endTimeStamp += 86399;
                    $endTimeStamp = $this->helperTimeStamp->getLocalTimeStamp($endTimeStamp);
                }

                $arrayResult = [
                    'success'         => true,
                    'beforeTimerText' => $countdownTimerData[CountdownTimerInterface::BEFORE_TIMER_TEXT],
                    'afterTimerText'  => $countdownTimerData[CountdownTimerInterface::AFTER_TIMER_TEXT],
                    'theme'           => $countdownTimerData[CountdownTimerInterface::THEME],
                    'accent'          => $countdownTimerData[CountdownTimerInterface::ACCENT],
                    'timeStamp'       => $endTimeStamp
                ];
            } catch (NoSuchEntityException $e) {
                $arrayResult = [
                    'success' => false,
                    'message' => $e->getMessage()
                ];
            } catch (\Exception $e) {
                $this->logger->critical($e);
                $arrayResult = [
                    'message' => __('There was an error loading Countdown Timer data.')
                ];
                $result->setHttpResponseCode(500);
            }

            return $result->setData($arrayResult);
        }

        return null;
    }
}
