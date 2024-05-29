<?php

namespace Meetanshi\Bulksms\Model;

use Magento\Framework\Model\AbstractModel;

class Bulksms extends AbstractModel
{
    protected function _construct()
    {
        $this->_init('Meetanshi\Bulksms\Model\ResourceModel\Bulksms');
    }
}
