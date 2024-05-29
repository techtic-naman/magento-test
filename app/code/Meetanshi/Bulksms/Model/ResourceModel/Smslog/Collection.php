<?php

namespace Meetanshi\Bulksms\Model\ResourceModel\Smslog;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    protected function _construct()
    {
        $this->_init(
            'Meetanshi\Bulksms\Model\Smslog',
            'Meetanshi\Bulksms\Model\ResourceModel\Smslog'
        );
    }
}
