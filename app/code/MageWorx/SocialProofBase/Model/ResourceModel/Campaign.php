<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace MageWorx\SocialProofBase\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use MageWorx\SocialProofBase\Api\Data\CampaignInterface;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\DataObject;

class Campaign extends AbstractDb
{
    const CAMPAIGN_TABLE                = 'mageworx_socialproofbase_campaign';
    const CAMPAIGN_STORE_TABLE          = 'mageworx_socialproofbase_campaign_store';
    const CAMPAIGN_CUSTOMER_GROUP_TABLE = 'mageworx_socialproofbase_campaign_customer_group';
    const CAMPAIGN_DISPLAY_ON_TABLE     = 'mageworx_socialproofbase_campaign_display_on';
    const CAMPAIGN_PRODUCT_TABLE        = 'mageworx_socialproofbase_campaign_product';
    const CAMPAIGN_CATEGORY_TABLE       = 'mageworx_socialproofbase_campaign_category';
    const CAMPAIGN_CMS_PAGE_TABLE       = 'mageworx_socialproofbase_campaign_cms_page';
    const CAMPAIGN_TEMPLATE_TABLE       = 'mageworx_socialproofbase_campaign_template';

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct(): void
    {
        $this->_init(self::CAMPAIGN_TABLE, CampaignInterface::CAMPAIGN_ID);
    }

    /**
     * @param AbstractModel $object
     * @return AbstractDb
     */
    protected function _afterSave(AbstractModel $object): AbstractDb
    {
        $this->assignCampaignToStoreViews($object);
        $this->assignCampaignToCustomerGroups($object);
        $this->assignCampaignToPageTypes($object);
        $this->assignCampaignToProducts($object);
        $this->assignCampaignToCategories($object);
        $this->assignCampaignToCmsPages($object);

        return parent::_afterSave($object);
    }

    /**
     * @param CampaignInterface|AbstractModel $campaign
     */
    protected function assignCampaignToStoreViews($campaign): void
    {
        $this->assignCampaignToEntity($campaign, self::CAMPAIGN_STORE_TABLE, CampaignInterface::STORE_IDS);
    }

    /**
     * @param CampaignInterface|AbstractModel $campaign
     */
    protected function assignCampaignToCustomerGroups($campaign): void
    {
        $this->assignCampaignToEntity(
            $campaign,
            self::CAMPAIGN_CUSTOMER_GROUP_TABLE,
            CampaignInterface::CUSTOMER_GROUP_IDS
        );
    }

    /**
     * @param CampaignInterface|AbstractModel $campaign
     */
    protected function assignCampaignToPageTypes($campaign): void
    {
        $where = CampaignInterface::CAMPAIGN_ID . ' = "' . $campaign->getId() . '"';

        $this->getConnection()->delete(
            $this->getTable(self::CAMPAIGN_DISPLAY_ON_TABLE),
            $where
        );

        $pageTypes = $campaign->getDisplayOn();

        if (!empty($pageTypes)) {
            $data = [];

            foreach ($pageTypes as $type) {
                $data[] = [
                    CampaignInterface::CAMPAIGN_ID => $campaign->getId(),
                    'page_type'                    => $type
                ];
            }

            $this->getConnection()->insertMultiple(
                $this->getTable(self::CAMPAIGN_DISPLAY_ON_TABLE),
                $data
            );
        }
    }

    /**
     * @param CampaignInterface|AbstractModel $campaign
     */
    protected function assignCampaignToProducts($campaign): void
    {
        $this->assignCampaignToEntity($campaign, self::CAMPAIGN_PRODUCT_TABLE, CampaignInterface::PRODUCT_IDS);
    }

    /**
     * @param CampaignInterface|AbstractModel $campaign
     */
    protected function assignCampaignToCategories($campaign): void
    {
        $this->assignCampaignToEntity(
            $campaign,
            self::CAMPAIGN_CATEGORY_TABLE,
            CampaignInterface::CATEGORY_IDS
        );
    }

    /**
     * @param CampaignInterface|AbstractModel $campaign
     */
    protected function assignCampaignToCmsPages($campaign): void
    {
        $this->assignCampaignToEntity(
            $campaign,
            self::CAMPAIGN_CMS_PAGE_TABLE,
            CampaignInterface::CMS_PAGE_IDS
        );
    }

    /**
     * @param CampaignInterface|AbstractModel $campaign
     * @param string $tableName
     * @param string $fieldName
     */
    protected function assignCampaignToEntity($campaign, $tableName, $fieldName): void
    {
        $methodName = 'get' . str_replace('_', '', ucwords($fieldName, '_'));
        $columnName = rtrim($fieldName, 's');

        $newIds = (array)$campaign->{$methodName}();
        $oldIds = $this->getOldEntityIds($campaign->getId(), $tableName, $columnName);

        $table  = $this->getTable($tableName);
        $delete = array_diff($oldIds, $newIds);
        $insert = array_diff($newIds, $oldIds);

        if ($delete) {
            $where = [CampaignInterface::CAMPAIGN_ID . '= ?' => $campaign->getId(), $columnName . ' IN (?)' => $delete];
            $this->getConnection()->delete($table, $where);
        }

        if ($insert) {
            $data = [];

            foreach ($insert as $entityId) {
                $data[] = [CampaignInterface::CAMPAIGN_ID => $campaign->getId(), $columnName => $entityId];
            }
            $this->getConnection()->insertMultiple($table, $data);
        }
    }

    /**
     * @param int $campaignId
     * @param string $tableName
     * @param string $columnName
     * @return array
     */
    protected function getOldEntityIds($campaignId, $tableName, $columnName): array
    {
        $connection = $this->getConnection();

        $select = $connection->select()->from(
            $this->getTable($tableName),
            $columnName
        )->where(
            CampaignInterface::CAMPAIGN_ID . ' = ?',
            $campaignId
        );

        return $connection->fetchCol($select);
    }

    /**
     * @param AbstractModel|DataObject $object
     * @return Campaign
     */
    protected function _afterLoad(AbstractModel $object): Campaign
    {
        $this->addAssociatedStoreViews($object);
        $this->addAssociatedCustomerGroups($object);
        $this->addAssociatedPageTypes($object);
        $this->addAssociatedProducts($object);
        $this->addAssociatedCategories($object);
        $this->addAssociatedCmsPages($object);

        return parent::_afterLoad($object);
    }

    /**
     * @param AbstractModel|DataObject $object
     */
    protected function addAssociatedStoreViews(AbstractModel $object): void
    {
        $this->updateObjectUsingAssociatedEntities(
            $object,
            self::CAMPAIGN_STORE_TABLE,
            CampaignInterface::STORE_IDS
        );
    }

    /**
     * @param AbstractModel|DataObject $object
     */
    protected function addAssociatedCustomerGroups(AbstractModel $object): void
    {
        $this->updateObjectUsingAssociatedEntities(
            $object,
            self::CAMPAIGN_CUSTOMER_GROUP_TABLE,
            CampaignInterface::CUSTOMER_GROUP_IDS
        );
    }

    /**
     * @param AbstractModel|DataObject $object
     */
    protected function addAssociatedPageTypes(AbstractModel $object): void
    {
        $this->updateObjectUsingAssociatedEntities(
            $object,
            self::CAMPAIGN_DISPLAY_ON_TABLE,
            CampaignInterface::DISPLAY_ON
        );
    }

    /**
     * @param AbstractModel|DataObject $object
     */
    protected function addAssociatedProducts(AbstractModel $object): void
    {
        $this->updateObjectUsingAssociatedEntities(
            $object,
            self::CAMPAIGN_PRODUCT_TABLE,
            CampaignInterface::PRODUCT_IDS
        );
    }

    /**
     * @param AbstractModel|DataObject $object
     */
    protected function addAssociatedCategories(AbstractModel $object): void
    {
        $this->updateObjectUsingAssociatedEntities(
            $object,
            self::CAMPAIGN_CATEGORY_TABLE,
            CampaignInterface::CATEGORY_IDS
        );
    }

    /**
     * @param AbstractModel|DataObject $object
     */
    protected function addAssociatedCmsPages(AbstractModel $object): void
    {
        $this->updateObjectUsingAssociatedEntities(
            $object,
            self::CAMPAIGN_CMS_PAGE_TABLE,
            CampaignInterface::CMS_PAGE_IDS
        );
    }

    /**
     * @param AbstractModel|DataObject $object
     * @param string $tableName
     * @param string $fieldName
     */
    protected function updateObjectUsingAssociatedEntities($object, $tableName, $fieldName): void
    {
        $id = $object->getId();

        if ($id) {
            $data       = [];
            $columnName = ($fieldName == CampaignInterface::DISPLAY_ON) ? 'page_type' : rtrim($fieldName, 's');
            $methodName = 'set' . str_replace('_', '', ucwords($fieldName, '_'));
            $connection = $this->getConnection();
            $select     = $connection->select()
                                     ->from($this->getTable($tableName))
                                     ->where(CampaignInterface::CAMPAIGN_ID . ' = ?', $id);

            $result = $connection->fetchAll($select);

            if ($result) {

                foreach ($result as $row) {
                    $data[] = $row[$columnName];
                }
            }

            $object->{$methodName}($data);
        }
    }
}
