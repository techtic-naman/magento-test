<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\RewardPoints\Block\Adminhtml\Customer\Edit\Tab\RewardPoints\Balance\Grid\Column\Renderer;

class ExpirationDate extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    /**
     * @var \MageWorx\RewardPoints\Helper\Data
     */
    protected $helperData;

    /**
     * @var \MageWorx\RewardPoints\Helper\ExpirationDate
     */
    protected $expirationDateHelper;

    /**
     * ExpirationDate constructor.
     *
     * @param \MageWorx\RewardPoints\Helper\Data $helperData
     * @param \MageWorx\RewardPoints\Helper\ExpirationDate $expirationDateHelper
     */
    public function __construct(
        \MageWorx\RewardPoints\Helper\Data $helperData,
        \MageWorx\RewardPoints\Helper\ExpirationDate $expirationDateHelper
    ) {
        $this->helperData = $helperData;
        $this->expirationDateHelper = $expirationDateHelper;
    }

    /**
     * @param \Magento\Framework\DataObject $row
     * @return \Magento\Framework\Phrase|string
     */
    public function render(\Magento\Framework\DataObject $row)
    {
        $websiteId = $row->getWebsiteId();
        $date = $row->getExpirationDate();

        if (!$this->helperData->isEnableExpirationDate($websiteId)) {
            return __('Unlimited (functionality is disabled)');
        }

        if ($date === null) {
            return __('Unlimited');
        }

        return  $this->expirationDateHelper->getExpirationPeriodFromDate(
            $date,
            \MageWorx\RewardPoints\Helper\ExpirationDate::FORMAT_PERIOD_FULL
        );
    }
}
