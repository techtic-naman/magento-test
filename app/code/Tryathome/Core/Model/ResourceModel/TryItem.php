<?php
namespace Tryathome\Core\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class TryItem extends AbstractDb
{
    protected function _construct()
    {
        $this->_init('tryathome_try', 'try_id'); // Table name and primary key column
    }
}