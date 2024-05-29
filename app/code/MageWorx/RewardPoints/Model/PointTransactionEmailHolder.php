<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\RewardPoints\Model;

class PointTransactionEmailHolder
{
    /**
     * @var array
     */
    protected $emailSenders = [];

    /**
     * @param \MageWorx\RewardPoints\Model\PointTransactionEmailSender $emailSender
     * @param \MageWorx\RewardPoints\Api\Data\PointTransactionInterface $pointTransaction
     * @param \Magento\Customer\Api\Data\CustomerInterface $customer
     */
    public function addEmailSendingData($emailSender, $pointTransaction, $customer)
    {
        $this->emailSenders[$pointTransaction->getEventCode()] = [
            'email_sender'      => $emailSender,
            'point_transaction' => $pointTransaction,
            'customer'          => $customer
        ];
    }

    /**
     * @param string $eventCode
     */
    public function sendEmail($eventCode)
    {
        if (!empty($this->emailSenders[$eventCode])) {
            /** @var \MageWorx\RewardPoints\Model\PointTransactionEmailSender $emailSender */
            $emailSender = $this->emailSenders[$eventCode]['email_sender'];

            /** @var \MageWorx\RewardPoints\Api\Data\PointTransactionInterface $pointTransaction */
            $pointTransaction = $this->emailSenders[$eventCode]['point_transaction'];

            /** @var \Magento\Customer\Api\Data\CustomerInterface $customer */
            $customer = $this->emailSenders[$eventCode]['customer'];

            $emailSender->sendPointTransactionEmail($pointTransaction, $customer);
        }
    }
}