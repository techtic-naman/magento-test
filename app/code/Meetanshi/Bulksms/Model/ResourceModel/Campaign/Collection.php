<?php

namespace Meetanshi\Bulksms\Model\ResourceModel\Campaign;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    protected function _construct()
    {
        $this->_init(
            'Meetanshi\Bulksms\Model\Campaign',
            'Meetanshi\Bulksms\Model\ResourceModel\Campaign'
        );
    }
}
