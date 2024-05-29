<?php
/**
 *
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\RewardPoints\Controller\Checkout;

use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Serialize\SerializerInterface;

class Cancel extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;

    /**
     * @var \MageWorx\RewardPoints\Model\QuoteTriggerSetter
     */
    protected $quoteTriggerSetter;

    /**
     * @var \Magento\Quote\Api\CartRepositoryInterface
     */
    protected $quoteRepository;

    /**
     * Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * @var SerializerInterface
     */
    protected $serializer;

    /**
     * Cancel constructor.
     *
     * @param \Magento\Framework\App\Action\Context $context
     * @param \MageWorx\RewardPoints\Model\QuoteTriggerSetter $quoteTriggerSetter
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Quote\Api\CartRepositoryInterface $quoteRepository
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \MageWorx\RewardPoints\Model\QuoteTriggerSetter $quoteTriggerSetter,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository,
        \Magento\Framework\Serialize\SerializerInterface $serializer,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->quoteTriggerSetter = $quoteTriggerSetter;
        $this->checkoutSession    = $checkoutSession;
        $this->quoteRepository    = $quoteRepository;
        $this->serializer         = $serializer;
        $this->logger             = $logger;
        parent::__construct($context);
    }

    /**
     * @return ResponseInterface|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        try {
            $quote = $this->checkoutSession->getQuote();
            if ($quote->getUseMwRewardPoints()) {
                $this->quoteTriggerSetter->setQuoteData($quote, false);
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
