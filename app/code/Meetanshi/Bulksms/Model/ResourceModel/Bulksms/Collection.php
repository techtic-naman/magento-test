<?php

namespace Meetanshi\Bulksms\Model\ResourceModel\Bulksms;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    protected function _construct()
    {
        $this->_init(
            'Meetanshi\Bulksms\Model\Bulksms',
            'Meetanshi\Bulksms\Model\ResourceModel\Bulksms'
        );
    }
}
