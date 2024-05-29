<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace MageWorx\SocialProofBase\Observer;

use Magento\Catalog\Model\ResourceModel\Eav\Attribute as EavAttribute;
use Magento\Framework\Event\Observer;
use MageWorx\SocialProofBase\Model\CampaignUpdater;
use Magento\Framework\Exception\LocalizedException;

class UpdateCampaignAfterDeleteAttributeObserver implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var CampaignUpdater
     */
    protected $campaignUpdater;

    /**
     * UpdateCampaignAfterDeleteAttributeObserver constructor.
     *
     * @param CampaignUpdater $campaignUpdater
     */
    public function __construct(CampaignUpdater $campaignUpdater)
    {
        $this->campaignUpdater = $campaignUpdater;
    }

    /**
     * @param Observer $observer
     * @throws LocalizedException
     */
    public function execute(Observer $observer): void
    {
        /** @var EavAttribute $attribute */
        $attribute = $observer->getEvent()->getAttribute();

        if ($this->isNeedUpdate($attribute)) {
            $this->campaignUpdater->updateCampaignByAttributeCode($attribute->getAttributeCode());
        }
    }

    /**
     * @param EavAttribute $attribute
     * @return bool
     */
    protected function isNeedUpdate($attribute): bool
    {
        return (bool)$attribute->getIsUsedForPromoRules();
    }
}
