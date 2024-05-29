<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\RewardPoints\Model\Source;

/**
 * Source model for websites with "All Websites" option
 */
class Website implements \Magento\Framework\Option\ArrayInterface
{
    const ALL_WEBSITES = '';

    /**
     * Core system store model
     *
     * @var \Magento\Store\Model\System\Store
     */
    protected $store;

    /**
     * @param \Magento\Store\Model\System\Store $store
     */
    public function __construct(\Magento\Store\Model\System\Store $store)
    {
        $this->store = $store;
    }

    /**
     * @param bool $isAddAllWebsites
     * @return array
     */
    public function toOptionArray($isAddAllWebsites = true)
    {
        $websites = [];

        if ($isAddAllWebsites) {
            $websites[] = ['value' => self::ALL_WEBSITES, 'label' => __('All Websites')];
        }

        return $websites + $this->store->getWebsiteOptionHash();
    }
}
