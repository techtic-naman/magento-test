<?php

namespace Meetanshi\Bulksms\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Bulksms extends AbstractDb
{
    protected function _construct()
    {
        $this->_init('mt_bulksms', 'id');
    }
}
