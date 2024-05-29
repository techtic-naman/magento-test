<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\RewardPoints\Model\Plugin;

use Magento\Framework\Model\AbstractModel;
use Magento\Sales\Model\ResourceModel\Order\Creditmemo as CreditMemoResourceModel;

class IncreasePointsByRefundPlugin
{
    /**
     * @var \MageWorx\RewardPoints\Helper\Data
     */
    protected $helperData;

    /**
     * @var \MageWorx\RewardPoints\Model\PointTransactionApplier
     */
    protected $pointTransactionApplier;

    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    protected $customerRepository;

    /**
     * @var string
     */
    protected $eventCode = \MageWorx\RewardPoints\Model\EventStrategyFactory::REFUND_EVENT;

    /**
     * IncreasePointsByRefundPlugin constructor.
     *
     * @param \MageWorx\RewardPoints\Helper\Data $helperData
     * @param \MageWorx\RewardPoints\Model\PointTransactionApplier $pointTransactionApplier
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
     */
    public function __construct(
        \MageWorx\RewardPoints\Helper\Data $helperData,
        \MageWorx\RewardPoints\Model\PointTransactionApplier $pointTransactionApplier,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
    ) {
        $this->helperData              = $helperData;
        $this->pointTransactionApplier = $pointTransactionApplier;
        $this->customerRepository      = $customerRepository;
    }

    /**
     * Check if refund is allowed and set reward data before save creditmemo
     *
     * @param CreditMemoResourceModel $subject
     * @param AbstractModel $creditmemo
     * @return void
     */
    public function beforeSave(CreditMemoResourceModel $subject, AbstractModel $creditmemo)
    {
        if ($creditmemo->getBaseMwRwrdpointsCurAmnt()) {

            /* @var $order \Magento\Sales\Model\Order */
            $order = $creditmemo->getOrder();

            $order->setMwRwrdpointsAmntRefunded(
                $order->getMwRwrdpointsAmntRefunded() + $creditmemo->getMwRwrdpointsAmnt()
            );
            $order->setMwRwrdpointsCurAmntRefund(
                $order->getMwRwrdpointsCurAmntRefund() + $creditmemo->getMwRwrdpointsCurAmnt()
            );
            $order->setBaseMwRwrdpointsCurAmntRefund(
                $order->getBaseMwRwrdpointsCurAmntRefund() + $creditmemo->getBaseMwRwrdpointsCurAmnt()
            );
            $order->setMwRwrdpointsAmntRefund(
                $order->getMwRwrdpointsAmntRefund() + $creditmemo->getMwRwrdpointsAmntRefund()
            );

            $customer = $this->customerRepository->getById($order->getCustomerId());

            $this->pointTransactionApplier->applyTransaction(
                $this->eventCode,
                $customer,
                $order->getStoreId(),
                $creditmemo
            );
        }
    }
}
