<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\RewardPoints\Model\Plugin;

use Magento\Framework\Model\AbstractModel;
use Magento\Sales\Model\ResourceModel\Order\Creditmemo as CreditMemoResourceModel;

class DecreasePointsByRefundPlugin
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
    protected $eventCode = \MageWorx\RewardPoints\Model\EventStrategyFactory::REFUND_DECREASE_POINTS_EVENT;
    /**
     * @var \Magento\Catalog\Model\Product\Type
     */
    protected $productType;

    /**
     * DecreasePointsByRefundPlugin constructor.
     *
     * @param \MageWorx\RewardPoints\Helper\Data $helperData
     * @param \MageWorx\RewardPoints\Model\PointTransactionApplier $pointTransactionApplier
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
     */
    public function __construct(
        \MageWorx\RewardPoints\Helper\Data $helperData,
        \MageWorx\RewardPoints\Model\PointTransactionApplier $pointTransactionApplier,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Magento\Catalog\Model\Product\Type $productType
    ) {
        $this->helperData              = $helperData;
        $this->pointTransactionApplier = $pointTransactionApplier;
        $this->customerRepository      = $customerRepository;
        $this->productType             = $productType;
    }

    /**
     * Decrease customer points balance throught refund after save
     *
     * @param CreditMemoResourceModel $subject
     * @param CreditMemoResourceModel $result
     * @param AbstractModel $creditmemo
     * @return CreditMemoResourceModel
     */
    public function afterSave(
        CreditMemoResourceModel $subject,
        CreditMemoResourceModel $result,
        AbstractModel $creditmemo
    ) {
        /* @var $order \Magento\Sales\Model\Order */
        $order = $creditmemo->getOrder();

        if (!$order->getCustomerId()) {
            return $result;
        }

        if (!$this->helperData->getIsReturnPointsOnRefund()) {
            return $result;
        }

        if (!$order->getTotalQtyOrdered()) {
            return $result;
        }

        if (!$this->getIsFullRefund($order)) {
            return $result;
        }

        $customer = $this->customerRepository->getById($order->getCustomerId());

        $this->pointTransactionApplier->applyTransaction(
            $this->eventCode,
            $customer,
            $order->getStoreId(),
            $order
        );

        return $result;
    }


    /**
     * @param \Magento\Sales\Model\Order $order
     * @return bool|int
     */
    protected function getIsFullRefund($order)
    {
        $creditmemosCollection = $order->getCreditmemosCollection();

        if (!$creditmemosCollection) {
            return false;
        }

        return $order->getTotalQtyOrdered() && $order->getTotalQtyOrdered() == $this->getTotalItemsToRefund($order);
    }

    /**
     * @param \Magento\Sales\Model\Order $order
     * @return int
     */
    protected function getTotalItemsToRefund(
        \Magento\Sales\Model\Order $order
    ) {
        $totalItemsRefund = 0;
        $compositeTypes   = $this->productType->getCompositeTypes();

        foreach ($order->getCreditmemosCollection() as $creditMemo) {
            foreach ($creditMemo->getAllItems() as $item) {
                $orderItem = $order->getItemById($item->getOrderItemId());

                if (in_array($orderItem->getProductType(), $compositeTypes)) {
                    continue;
                }

                $totalItemsRefund += $item->getQty();
            }
        }

        return (int)$totalItemsRefund;
    }
}
