<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\RewardPoints\Model\Plugin;

class UpdateCustomerRewardSectionPlugin
{
    /**
     * @var \MageWorx\RewardPoints\Helper\Data
     */
    protected $helperData;

    /**
     * AddDashboardTabPlugin constructor.
     *
     * @param \MageWorx\RewardPoints\Helper\Data $helperData
     */
    public function __construct(
        \MageWorx\RewardPoints\Helper\Data $helperData
    ) {
        $this->helperData = $helperData;
    }

    /**
     * We can't foresee all actions for cleaning point's data, we assume it will be needed every time
     * when cart will be changed
     *
     * @param \Magento\Customer\CustomerData\SectionPoolInterface $subject
     * @param $sectionNames
     * @return array
     */
    public function beforeGetSectionsData(\Magento\Customer\CustomerData\SectionPoolInterface $subject, $sectionNames)
    {
        if (!empty($sectionNames)
            && in_array('cart', $sectionNames)
            && !in_array('upcoming-points', $sectionNames)
            && $this->helperData->isEnableForCustomer()
            && $this->helperData->isDisplayMinicartPointBalanceMessage()
        ) {
            $sectionNames[] = 'upcoming-points';
        }

        return [$sectionNames];
    }
}