<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\PersonalPromotion\Observer\Backend;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\DataObject;

class AddHistoricalConditionsObserver implements ObserverInterface
{
    /**
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        /** @var DataObject $additional */
        $additional           = $observer->getAdditional();
        $additionalConditions = (array)$additional->getConditions();
        $conditions           = array_merge_recursive($additionalConditions, [$this->getHistoricalConditions()]);

        $additional->setConditions($conditions);
    }

    /**
     * @return array
     */
    protected function getHistoricalConditions()
    {
        $attributes = [
            [
                'value' => \MageWorx\PersonalPromotion\Model\Rule\Condition\PurchasedAmount::class,
                'label' => __('Purchased amount')
            ],
            [
                'value' => \MageWorx\PersonalPromotion\Model\Rule\Condition\PurchasedSku::class,
                'label' => __('Purchased SKU(s)')
            ]
        ];

        return ['label' => __('Historical Conditions'), 'value' => $attributes];
    }
}
