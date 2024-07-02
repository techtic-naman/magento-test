<?php
namespace Tryathome\Core\Model\ResourceModel\TryItem;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    protected $_idFieldName = 'try_id';
    protected $_eventPrefix = 'tryathome_try_collection';
    protected $_eventObject = 'try_collection';

    protected function _construct()
    {
        $this->_init(
            \Tryathome\Core\Model\TryItem::class,
            \Tryathome\Core\Model\ResourceModel\TryItem::class
        );
    }
}