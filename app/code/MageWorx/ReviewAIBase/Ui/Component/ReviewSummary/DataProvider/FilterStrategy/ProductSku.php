<?php

namespace MageWorx\ReviewAIBase\Ui\Component\ReviewSummary\DataProvider\FilterStrategy;

use Magento\Framework\Data\Collection;
use Magento\Ui\DataProvider\AddFilterToCollectionInterface;

class ProductSku implements AddFilterToCollectionInterface
{
    /**
     * @param Collection $collection
     * @param string $field
     * @param string|null $condition
     * @return void
     */
    public function addFilter(Collection $collection, $field, $condition = null)
    {
        $field = 'catalog_product_entity.sku';
        $collection->addFieldToFilter($field, $condition);
    }
}
