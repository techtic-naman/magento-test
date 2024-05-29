<?php

namespace Meetanshi\Bulksms\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

class Apiprovider implements ArrayInterface
{
    public function toOptionArray()
    {
        return [
            ['value' => 'msg91', 'label' => __('Msg91')],
            ['value' => 'textlocal', 'label' => __('Text Local')],
            ['value' => 'twilio', 'label' => __('Twilio')],
        ];
    }
}
