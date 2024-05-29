<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\RewardPoints\Block\Sales\Order;

class Total extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \MageWorx\RewardPoints\Helper\Data
     */
    protected $helperData;

    /**
     * @var \MageWorx\RewardPoints\Helper\Price
     */
    protected $helperPrice;

    /**
     * Total constructor.
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \MageWorx\RewardPoints\Helper\Data $helperData
     * @param \MageWorx\RewardPoints\Helper\Price $helperPrice
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \MageWorx\RewardPoints\Helper\Data $helperData,
        \MageWorx\RewardPoints\Helper\Price $helperPrice,
        array $data = []
    ) {
        $this->helperData  = $helperData;
        $this->helperPrice = $helperPrice;
        parent::__construct($context, $data);
    }

    /**
     * @return \Magento\Sales\Model\Order
     */
    public function getOrder()
    {
        return $this->getParentBlock()->getOrder();
    }

    /**
     * Get totals source object
     *
     * @return \Magento\Sales\Model\Order
     */
    public function getSource()
    {
        return $this->getParentBlock()->getSource();
    }

    /**
     * @return $this
     */
    public function initTotals()
    {
        if ($this->getOrder() && (double)$this->getOrder()->getBaseMwRwrdpointsCurAmnt()) {
            $source = $this->getSource();
            $value  = -$source->getMwRwrdpointsCurAmnt();

            $this->getParentBlock()->addTotal(
                new \Magento\Framework\DataObject(
                    [
                        'code'   => 'mageworx_rewardpoints',
                        'strong' => false,
                        'label'  => $this->helperPrice->getFormattedPoints($source->getMwRwrdpointsAmnt()),
                        'value'  => $source instanceof \Magento\Sales\Model\Order\Creditmemo ? -$value : $value,
                    ]
                )
            );
        }

        return $this;
    }
}
