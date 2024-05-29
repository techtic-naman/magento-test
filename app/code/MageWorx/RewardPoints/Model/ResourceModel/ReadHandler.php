<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\RewardPoints\Model\ResourceModel;

use Magento\Framework\EntityManager\Operation\AttributeInterface;

/**
 * Class ReadHandler
 *
 * We don't use virtual type for avoid the excessive dependence from the sales rule read handler
 */
class ReadHandler implements AttributeInterface
{
    /**
     * @var \MageWorx\RewardPoints\Model\ResourceModel\Rule
     */
    protected $ruleResource;

    /**
     * @var \Magento\Framework\EntityManager\MetadataPool
     */
    protected $metadataPool;

    /**
     * @param Rule $ruleResource
     * @param \Magento\Framework\EntityManager\MetadataPool $metadataPool
     */
    public function __construct(
        \MageWorx\RewardPoints\Model\ResourceModel\Rule $ruleResource,
        \Magento\Framework\EntityManager\MetadataPool $metadataPool
    ) {
        $this->ruleResource = $ruleResource;
        $this->metadataPool = $metadataPool;
    }

    /**
     * @param string $entityType
     * @param array $entityData
     * @param array $arguments
     * @return array
     * @throws \Exception
     */
    public function execute($entityType, $entityData, $arguments = [])
    {
        $linkField = $this->metadataPool->getMetadata($entityType)->getLinkField();
        $entityId  = $entityData[$linkField];

        $entityData['customer_group_ids'] = $this->ruleResource->getCustomerGroupIds($entityId);
        $entityData['website_ids']        = $this->ruleResource->getWebsiteIds($entityId);


        return $entityData;
    }
}
