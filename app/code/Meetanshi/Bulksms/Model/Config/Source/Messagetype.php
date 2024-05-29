<?php

namespace Meetanshi\Bulksms\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

class Messagetype implements ArrayInterface
{
    public function toOptionArray()
    {
        return [
            ['value' => '1', 'label' => __('Promotional')],
            ['value' => '4', 'label' => __('Transactional')],
        ];
    }
}
