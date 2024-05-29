<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace MageWorx\SocialProofBase\Model;

use Magento\CatalogRule\Model\Rule\Condition\Combine as ConditionCombine;
use Magento\CatalogRule\Model\Rule\Condition\CombineFactory as ConditionCombineFactory;
use Magento\Framework\Serialize\Serializer\Json as SerializerJson;
use Magento\Rule\Model\Condition\Product\AbstractProduct as ConditionAbstractProduct;
use MageWorx\SocialProofBase\Api\CampaignRepositoryInterface;
use MageWorx\SocialProofBase\Model\ResourceModel\Campaign\CollectionFactory as CampaignCollectionFactory;
use MageWorx\SocialProofBase\Model\ResourceModel\Campaign\Collection as CampaignCollection;
use MageWorx\SocialProofBase\Model\Campaign;
use MageWorx\SocialProofBase\Model\Source\Campaign\Status as StatusOptions;
use Magento\Framework\Message\ManagerInterface as MessageManagerInterface;
use Magento\Framework\Exception\LocalizedException;

class CampaignUpdater
{
    /**
     * @var CampaignCollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var MessageManagerInterface
     */
    protected $messageManager;

    /**
     * @var ConditionCombineFactory
     */
    protected $conditionCombineFactory;

    /**
     * @var SerializerJson
     */
    protected $serializerJson;

    /**
     * @var CampaignRepositoryInterface
     */
    protected $campaignRepository;

    /**
     * CampaignUpdater constructor.
     *
     * @param CampaignCollectionFactory $collectionFactory
     * @param MessageManagerInterface $messageManager
     * @param ConditionCombineFactory $conditionCombineFactory
     * @param SerializerJson $serializerJson
     * @param CampaignRepositoryInterface $campaignRepository
     */
    public function __construct(
        CampaignCollectionFactory $collectionFactory,
        MessageManagerInterface $messageManager,
        ConditionCombineFactory $conditionCombineFactory,
        SerializerJson $serializerJson,
        CampaignRepositoryInterface $campaignRepository
    ) {
        $this->collectionFactory       = $collectionFactory;
        $this->messageManager          = $messageManager;
        $this->conditionCombineFactory = $conditionCombineFactory;
        $this->serializerJson          = $serializerJson;
        $this->campaignRepository      = $campaignRepository;
    }

    /**
     * @param string $attributeCode
     * @return CampaignUpdater
     * @throws LocalizedException
     */
    public function updateCampaignByAttributeCode($attributeCode): CampaignUpdater
    {
        /** @var CampaignCollection $collection */
        $collection = $this->collectionFactory->create();
        $collection->addAttributeInConditionFilter($attributeCode);

        $count = 0;

        foreach ($collection as $campaign) {
            /* @var Campaign $campaign */
            $campaign->setStatus(StatusOptions::DISABLE);

            if ($campaign->getDisplayOnProductsConditionsSerialized()) {
                $conditions = $this->getConditions($campaign->getDisplayOnProductsConditionsSerialized());

                $this->removeAttributeFromConditions($conditions, $attributeCode);

                $campaign->setDisplayOnProductsConditionsSerialized(
                    $this->serializerJson->serialize($conditions->asArray())
                );
            }

            if ($campaign->getRestrictionConditionsSerialized()) {
                $conditions = $this->getConditions($campaign->getRestrictionConditionsSerialized());

                $this->removeAttributeFromConditions($conditions, $attributeCode);

                $campaign->setRestrictionConditionsSerialized(
                    $this->serializerJson->serialize($conditions->asArray())
                );
            }

            $this->campaignRepository->save($campaign);

            $count++;
        }

        if ($count) {
            $this->messageManager->addWarningMessage(
                __(
                    '%1 Campaigns based on "%2" attribute have been disabled.',
                    $count,
                    $attributeCode
                )
            );
        }

        return $this;
    }

    /**
     * @param string $conditionsSerialized
     * @return ConditionCombine
     */
    protected function getConditions($conditionsSerialized): ConditionCombine
    {
        /** @var ConditionCombine $conditionsObj */
        $conditions = $this->conditionCombineFactory->create();

        $conditions->setPrefix('conditions');
        $conditions->loadArray($this->serializerJson->unserialize($conditionsSerialized));

        return $conditions;
    }

    /**
     * Remove catalog attribute condition by attribute code from conditions
     *
     * @param ConditionCombine $conditionCombine
     * @param string $attributeCode
     * @return void
     */
    protected function removeAttributeFromConditions(ConditionCombine $conditionCombine, $attributeCode): void
    {
        $conditions = $conditionCombine->getConditions();

        foreach ($conditions as $id => $condition) {

            if ($condition instanceof ConditionCombine) {
                $this->removeAttributeFromConditions($condition, $attributeCode);
            }

            if ($condition instanceof ConditionAbstractProduct) {
                if ($condition->getAttribute() == $attributeCode) {
                    unset($conditions[$id]);
                }
            }
        }
        $conditionCombine->setConditions($conditions);
    }
}
