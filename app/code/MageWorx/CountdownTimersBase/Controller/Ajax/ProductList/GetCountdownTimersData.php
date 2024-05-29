<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace MageWorx\CountdownTimersBase\Controller\Ajax\ProductList;

use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\Json as ResultJson;
use Magento\Framework\Controller\Result\JsonFactory as ResultJsonFactory;
use Magento\Framework\Exception\NoSuchEntityException;
use MageWorx\CountdownTimersBase\Api\Data\CountdownTimerInterface;
use MageWorx\CountdownTimersBase\Api\FrontendCountdownTimerListResolverInterface;
use Psr\Log\LoggerInterface;

class GetCountdownTimersData extends \Magento\Framework\App\Action\Action
{
    /**
     * @var FrontendCountdownTimerListResolverInterface
     */
    protected $frontendCountdownTimerListResolver;

    /**
     * @var ResultJsonFactory
     */
    protected $resultJsonFactory;

    /**
     * @var CustomerSession
     */
    protected $customerSession;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * GetCountdownTimersData constructor.
     *
     * @param Context $context
     * @param FrontendCountdownTimerListResolverInterface $frontendCountdownTimerListResolver
     * @param ResultJsonFactory $resultJsonFactory
     * @param CustomerSession $customerSession
     * @param LoggerInterface $logger
     */
    public function __construct(
        Context $context,
        FrontendCountdownTimerListResolverInterface $frontendCountdownTimerListResolver,
        ResultJsonFactory $resultJsonFactory,
        CustomerSession $customerSession,
        LoggerInterface $logger
    ) {
        parent::__construct($context);
        $this->frontendCountdownTimerListResolver = $frontendCountdownTimerListResolver;
        $this->resultJsonFactory                  = $resultJsonFactory;
        $this->customerSession                    = $customerSession;
        $this->logger                             = $logger;
    }

    /**
     * @return ResultJson|null
     */
    public function execute(): ?ResultJson
    {
        if ($this->getRequest()->isAjax()) {
            $result = $this->resultJsonFactory->create();

            try {
                $countdownTimers = $this->frontendCountdownTimerListResolver->getCountdownTimers(
                    $this->customerSession->getCustomerGroupId(),
                    $this->getRequest()->getParam('productIds', [])
                );

                if (empty($countdownTimers)) {
                    throw new NoSuchEntityException(__('Requested Countdown Timers doesn\'t exist'));
                }

                foreach ($countdownTimers as $productId => $countdownTimerData) {
                    $arrayResult['timersData'][$productId] = $this->getPreparedData($countdownTimerData);
                }
            } catch (NoSuchEntityException $e) {
                $arrayResult = [
                    'success' => false,
                    'message' => $e->getMessage()
                ];
            } catch (\Exception $e) {
                $this->logger->critical($e);
                $arrayResult = [
                    'message' => __('There was an error loading Countdown Timers data.')
                ];
                $result->setHttpResponseCode(500);
            }

            return $result->setData($arrayResult);
        }

        return null;
    }

    /**
     * @param array $countdownTimerData
     * @return array
     */
    protected function getPreparedData(array $countdownTimerData): array
    {
        return [
            'beforeTimerText' => $countdownTimerData[CountdownTimerInterface::BEFORE_TIMER_TEXT],
            'afterTimerText'  => $countdownTimerData[CountdownTimerInterface::AFTER_TIMER_TEXT],
            'theme'           => $countdownTimerData[CountdownTimerInterface::THEME],
            'accent'          => $countdownTimerData[CountdownTimerInterface::ACCENT],
            'timeStamp'       => $countdownTimerData['time_stamp'],
            'size'            => 'small'
        ];
    }
}
