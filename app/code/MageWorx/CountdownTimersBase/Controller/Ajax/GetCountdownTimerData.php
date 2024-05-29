<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace MageWorx\CountdownTimersBase\Controller\Ajax;

use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory as ResultJsonFactory;
use Magento\Framework\Controller\Result\Json as ResultJson;
use MageWorx\CountdownTimersBase\Api\FrontendCountdownTimerResolverInterface;
use MageWorx\CountdownTimersBase\Api\Data\CountdownTimerInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\Exception\NoSuchEntityException;
use Psr\Log\LoggerInterface;

class GetCountdownTimerData extends \Magento\Framework\App\Action\Action
{
    /**
     * @var ResultJsonFactory
     */
    protected $resultJsonFactory;

    /**
     * @var FrontendCountdownTimerResolverInterface
     */
    protected $frontendCountdownTimerResolver;

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
     * GetCountdownTimerData constructor.
     *
     * @param Context $context
     * @param ResultJsonFactory $resultJsonFactory
     * @param FrontendCountdownTimerResolverInterface $frontendCountdownTimerResolver
     * @param StoreManagerInterface $storeManager
     * @param CustomerSession $customerSession
     * @param LoggerInterface $logger
     */
    public function __construct(
        Context $context,
        ResultJsonFactory $resultJsonFactory,
        FrontendCountdownTimerResolverInterface $frontendCountdownTimerResolver,
        StoreManagerInterface $storeManager,
        CustomerSession $customerSession,
        LoggerInterface $logger
    ) {
        parent::__construct($context);

        $this->resultJsonFactory              = $resultJsonFactory;
        $this->frontendCountdownTimerResolver = $frontendCountdownTimerResolver;
        $this->storeManager                   = $storeManager;
        $this->customerSession                = $customerSession;
        $this->logger                         = $logger;
    }

    /**
     * @return ResultJson|null
     */
    public function execute(): ?ResultJson
    {
        if ($this->getRequest()->isAjax()) {
            $result = $this->resultJsonFactory->create();

            try {
                $countdownTimer = $this->frontendCountdownTimerResolver->getCountdownTimer(
                    $this->storeManager->getStore()->getId(),
                    $this->customerSession->getCustomerGroupId(),
                    (int)$this->getRequest()->getParam('productId')
                );

                if (!$countdownTimer) {
                    throw new NoSuchEntityException(__('Requested Countdown Timer doesn\'t exist'));
                }

                $arrayResult = [
                    'success'         => true,
                    'beforeTimerText' => $countdownTimer->getData(CountdownTimerInterface::BEFORE_TIMER_TEXT),
                    'afterTimerText'  => $countdownTimer->getData(CountdownTimerInterface::AFTER_TIMER_TEXT),
                    'theme'           => $countdownTimer->getData(CountdownTimerInterface::THEME),
                    'accent'          => $countdownTimer->getData(CountdownTimerInterface::ACCENT),
                    'timeStamp'       => $countdownTimer->getData('time_stamp')
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
