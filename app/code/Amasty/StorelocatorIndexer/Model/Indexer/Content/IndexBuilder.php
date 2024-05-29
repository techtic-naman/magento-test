<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Store Locator Indexer for Magento 2 (System)
 */

namespace Amasty\StorelocatorIndexer\Model\Indexer\Content;

use Amasty\Storelocator\Api\Data\LocationInterface;
use Amasty\Storelocator\Model\ConfigHtmlConverter;
use Amasty\Storelocator\Model\ResourceModel\Location\CollectionFactory as LocationCollectionFactory;
use Amasty\StorelocatorIndexer\Model\Indexer\AbstractIndexBuilder;
use Amasty\StorelocatorIndexer\Model\ResourceModel\LocationContentIndex;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;
use Magento\CatalogRule\Model\Indexer\IndexerTableSwapperInterface as TableSwapper;
use Psr\Log\LoggerInterface;

class IndexBuilder extends AbstractIndexBuilder
{
    public const INDEXER_ID = 'amasty_store_locator_content_indexer';

    /**
     * @var ConfigHtmlConverter
     */
    private $configHtmlConverter;

    /**
     * @var LocationContentIndex
     */
    private $contentIndex;

    /**
     * @var TableSwapper
     */
    private $tableSwapper;

    public function __construct(
        LocationCollectionFactory $locationCollectionFactory,
        LoggerInterface $logger,
        ProductCollectionFactory $productCollectionFactory,
        ConfigHtmlConverter $configHtmlConverter,
        LocationContentIndex $contentIndex,
        TableSwapper $tableSwapper,
        $batchSize = 1000
    ) {
        parent::__construct($locationCollectionFactory, $logger, $productCollectionFactory, $batchSize);
        $this->configHtmlConverter = $configHtmlConverter;
        $this->contentIndex = $contentIndex;
        $this->tableSwapper = $tableSwapper;
    }

    protected function doReindexByIds($ids)
    {
        $this->contentIndex->deleteByIds($ids);
        $locations = $this->locationCollectionFactory->create();
        $locations->joinMainImage();
        $locations->addFieldToFilter('main_table.' . LocationInterface::ID, ['in' => $ids]);
        $this->updateIndexData($locations->getItems());
    }

    protected function doReindexFull()
    {
        $tableName = $this->tableSwapper->getWorkingTableName($this->contentIndex->getMainTable());
        // unable to use getAllLocations, because we need all locations
        $locations = $this->locationCollectionFactory->create();
        $locations->joinMainImage();
        $this->updateIndexData($locations->getItems(), $tableName);
        $this->tableSwapper->swapIndexTables([$this->contentIndex->getMainTable()]);
    }

    private function updateIndexData(array $locations, string $tableName = LocationContentIndex::TABLE_NAME)
    {
        $insertData = [];
        $count = 0;
        foreach ($locations as $location) {
            $this->configHtmlConverter->setHtml($location);
            $insertData[] = [
                LocationContentIndex::LOCATION_ID => $location->getId(),
                LocationContentIndex::STORE_LIST_HTML => $location->getStoreListHtml(),
                LocationContentIndex::POPUP_HTML => $location->getPopupHtml()
            ];

            if (++$count == $this->batchSize) {
                $this->contentIndex->insertData($insertData, $tableName);
                $insertData = [];
                $count = 0;
            }
        }

        if (!empty($insertData)) {
            $this->contentIndex->insertData($insertData, $tableName);
        }
    }
}
