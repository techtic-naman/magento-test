<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\RewardPoints\Controller\Checkout;

use Magento\Checkout\Model\Session;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Quote\Api\CartRepositoryInterface;
use MageWorx\RewardPoints\Helper\Data;
use MageWorx\RewardPoints\Model\QuoteTriggerSetter;
use Psr\Log\LoggerInterface;

class Apply extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Quote\Api\CartRepositoryInterface
     */
    protected $quoteRepository;

    /**
     * Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * @var \MageWorx\RewardPoints\Model\QuoteTriggerSetter
     */
    protected $quoteTriggerSetter;

    /**
     * @var \MageWorx\RewardPoints\Helper\Data
     */
    protected $helperData;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;

    /**
     * @var SerializerInterface
     */
    protected $serializer;

    /**
     * Apply constructor.
     *
     * @param Context $context
     * @param CartRepositoryInterface $quoteRepository
     * @param Session $checkoutSession
     * @param QuoteTriggerSetter $quoteTriggerSetter
     * @param Data $helperData
     * @param SerializerInterface $serializer
     * @param LoggerInterface $logger
     */
    public function __construct(
        \Magento\Framework\App\Action\Context            $context,
        \Magento\Quote\Api\CartRepositoryInterface       $quoteRepository,
        \Magento\Checkout\Model\Session                  $checkoutSession,
        \MageWorx\RewardPoints\Model\QuoteTriggerSetter  $quoteTriggerSetter,
        \MageWorx\RewardPoints\Helper\Data               $helperData,
        \Magento\Framework\Serialize\SerializerInterface $serializer,
        \Psr\Log\LoggerInterface                         $logger
    ) {
        parent::__construct($context);
        $this->helperData         = $helperData;
        $this->quoteRepository    = $quoteRepository;
        $this->checkoutSession    = $checkoutSession;
        $this->quoteTriggerSetter = $quoteTriggerSetter;
        $this->serializer         = $serializer;
        $this->logger             = $logger;
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        try {
            if ($this->helperData->isEnableForCustomer()) {
                $amount = $this->getRequest()->getParam('amount');

                /* @var $quote \Magento\Quote\Model\Quote */
                $quote = $this->checkoutSession->getQuote();
                $this->quoteTriggerSetter->setQuoteData($quote, true, $amount);
                $quote->collectTotals();
                $this->quoteRepository->save($quote);
            }
        } catch (\Exception $e) {
            $this->logger->error($e);

            return $this->getResponse()->setBody($this->serializer->serialize(['result' => 'false']));
        }

        return $this->getResponse()->setBody($this->serializer->serialize(['result' => 'true']));
    }
}
