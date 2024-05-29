<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\RewardPoints\Block\Adminhtml\Order\Creditmemo;

class RewardPoints extends \Magento\Backend\Block\Template
{
    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * RewardPoints constructor.
     *
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
        $this->registry = $registry;
        parent::__construct($context, $data);
    }

    /**
     * @return \Magento\Sales\Model\Order\Creditmemo
     */
    public function getCreditmemo()
    {
        return $this->registry->registry('current_creditmemo');
    }

    /**
     * @return bool
     */
    public function getIsPointRefundAvailable()
    {
        $order = $this->getCreditmemo()->getOrder();

        if (!$order->getCustomerId() || $order->getMwRwrdpointsCurAmnt() <= 0) {
            return false;
        }

        return true;
    }

    /**
     * @return string
     */
    public function getPointRefundRangeLabel()
    {
        return __('(max %1 points)', $this->getRefundPointsBalance());
    }

    /**
     * @return string
     */
    public function getPointRefundRange()
    {
        return '0-' . $this->getRefundPointsBalance();
    }

    /**
     * Return points balance to refund
     *
     * @return double
     */
    public function getRefundPointsBalance()
    {
        return (double)$this->getCreditmemo()->getMwRwrdpointsAmnt();
    }
}
