<?php

namespace Meetanshi\OrderTracking\Block;

use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\View\Element\Template;
use Meetanshi\OrderTracking\Helper\Data as HelperData;

/**
 * Class OrderTrackingLink
 * @package Meetanshi\OrderTracking\Block
 */
class OrderTrackingLink extends Template
{
    /**
     * @var HelperData
     */
    protected $helper;

    /**
     * OrderTrackingLink constructor.
     * @param Context $context
     * @param HelperData $helper
     * @param array $data
     */
    public function __construct(Context $context, HelperData $helper, array $data = [])
    {
        $this->helper = $helper;
        parent::__construct($context, $data);
    }

    /**
     * @return HelperData
     */
    public function getHelper()
    {
        return $this->helper;
    }
}
