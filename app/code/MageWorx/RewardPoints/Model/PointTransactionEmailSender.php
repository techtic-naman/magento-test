<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\RewardPoints\Model;

use MageWorx\RewardPoints\Helper\ExpirationDate;

class PointTransactionEmailSender
{
    /**
     * @var \MageWorx\RewardPoints\Model\ResourceModel\PointTransaction
     */
    protected $pointTransactionResource;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \MageWorx\RewardPoints\Helper\Data
     */
    protected $helperData;

    /**
     * @var \Magento\Customer\Helper\View
     */
    protected $customerHelperView;

    /**
     * @var \Magento\Framework\Mail\Template\TransportBuilder
     */
    protected $transportBuilder;

    /**
     * @var \MageWorx\RewardPoints\Model\PointTransactionMessageMaker
     */
    protected $pointTransactionMessageMaker;

    /** @var \Psr\Log\LoggerInterface $logger */
    protected $logger;

    /**
     * PointTransactionEmailSender constructor.
     *
     * @param \MageWorx\RewardPoints\Helper\Data $helperData
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder
     * @param \Magento\Customer\Helper\View $customerHelperView
     * @param ResourceModel\PointTransaction $pointTransactionResource
     * @param PointTransactionMessageMaker $pointTransactionMessageMaker
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        \MageWorx\RewardPoints\Helper\Data $helperData,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        \Magento\Customer\Helper\View $customerHelperView,
        \MageWorx\RewardPoints\Model\ResourceModel\PointTransaction $pointTransactionResource,
        \MageWorx\RewardPoints\Model\PointTransactionMessageMaker $pointTransactionMessageMaker,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->helperData                   = $helperData;
        $this->storeManager                 = $storeManager;
        $this->transportBuilder             = $transportBuilder;
        $this->customerHelperView           = $customerHelperView;
        $this->pointTransactionResource     = $pointTransactionResource;
        $this->pointTransactionMessageMaker = $pointTransactionMessageMaker;
        $this->logger                       = $logger;
    }

    /**
     * @param \MageWorx\RewardPoints\Api\Data\PointTransactionInterface $pointTransaction
     * @param \Magento\Customer\Api\Data\CustomerInterface $customer
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function sendPointTransactionEmail($pointTransaction, $customer)
    {
        $templateId = $pointTransaction->getEmailTemplateId();
        $storeId    = $pointTransaction->getStoreId();

        if (!$templateId) {
            return $this;
        }

        $from = $this->helperData->getEmailSender($storeId);

        $this->transportBuilder->setTemplateIdentifier($templateId);
        $this->transportBuilder->setTemplateOptions(
            [
                'store' => $pointTransaction->getStoreId(),
                'area'  => \Magento\Framework\App\Area::AREA_FRONTEND
            ]
        );

        $message = $this->pointTransactionMessageMaker->getTransactionMessage(
            $pointTransaction->getEventCode(),
            $pointTransaction->getEventData(),
            $pointTransaction->getComment()
        );

        $expirationPeriod = $pointTransaction->getExpirationPeriod();
        $expirationDate   = $pointTransaction->getCustomerBalance()->getExpirationDate();

        $this->transportBuilder->setTemplateVars(
            [
                'customer_name'     => $this->customerHelperView->getCustomerName($customer),
                'store'             => $this->storeManager->getStore($storeId),
                'points_balance'    => $pointTransaction->getPointsBalance(),
                'points_since'      => $pointTransaction->getPointsBalance() - $pointTransaction->getPointsDelta(),
                'points_delta'      => $pointTransaction->getPointsDelta(),
                'expiration_period' => $expirationPeriod,
                'expiration_date'   => $expirationDate,
                'message'           => $message,
                'comment'           => $pointTransaction->getComment(),
            ]
        )->setFrom(
            $from
        )->addTo(
            $customer->getEmail(),
            $this->customerHelperView->getCustomerName($customer)
        );

        $transport = $this->transportBuilder->getTransport();

        try {
            $transport->sendMessage();
        } catch (\Magento\Framework\Exception\MailException $e) {
            $error = $e->getMessage();
            $this->logger->critical($error);
        }

        if (empty($error)) {
            $this->pointTransactionResource->addNotificationSentFlag($pointTransaction);
        }

        return $this;
    }

    /**
     * @param \MageWorx\RewardPoints\Api\Data\CustomerBalanceInterface $customerBalance
     * @param \Magento\Customer\Api\Data\CustomerInterface $customer
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function sendExpirationDateNotificationEmail($customerBalance, $customer)
    {
        $storeId    = $customer->getStoreId();
        $templateId = $this->helperData->getNotificationEmailTemplateId($storeId);

        if (!$templateId) {
            return $this;
        }

        $from = $this->helperData->getEmailSender($storeId);

        $this->transportBuilder->setTemplateIdentifier($templateId);
        $this->transportBuilder->setTemplateOptions(
            [
                'store' => $customer->getStoreId(),
                'area'  => \Magento\Framework\App\Area::AREA_FRONTEND
            ]
        );

        $expirationPeriod = $this->helperData->getDaysBeforeExpirationDateForNotification(
            $customerBalance->getWebsiteId()
        );

        $this->transportBuilder->setTemplateVars(
            [
                'customer_name'     => $this->customerHelperView->getCustomerName($customer),
                'store'             => $this->storeManager->getStore($storeId),
                'points_balance'    => $customerBalance->getPoints(),
                'expiration_period' => $expirationPeriod,
                'expiration_date'   => $customerBalance->getExpirationDate(),
            ]
        )->setFrom(
            $from
        )->addTo(
            $customer->getEmail(),
            $this->customerHelperView->getCustomerName($customer)
        );

        $transport = $this->transportBuilder->getTransport();

        try {
            $transport->sendMessage();
        } catch (\Magento\Framework\Exception\MailException $e) {
            $error = $e->getMessage();
            $this->logger->critical($error);
        }

        return $this;
    }

}