<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Store Locator Indexer for Magento 2 (System)
 */

namespace Amasty\StorelocatorIndexer\Model\Indexer\Product;

use Amasty\StorelocatorIndexer\Model\Indexer\AbstractIndexer;
use Magento\Catalog\Model\Product;

class ProductLocatorIndexer extends AbstractIndexer
{
    /**
     * @param int[] $ids
     */
    protected function doExecuteList($ids)
    {
        $this->indexBuilder->reindexByProductIds(array_unique($ids));
        $this->cacheContext->registerEntities(Product::CACHE_TAG, $ids);
    }

    /**
     * @param int $id
     */
    protected function doExecuteRow($id)
    {
        $this->indexBuilder->reindexByProductId($id);
    }
}
