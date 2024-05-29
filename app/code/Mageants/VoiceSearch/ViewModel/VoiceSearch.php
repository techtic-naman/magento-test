<?php

/**
 * @category Mageants Voice Search
 * @package Mageants_VoiceSearch
 * @copyright Copyright (c) 2017 Mageants
 * @author Mageants Team <support@mageants.com>
 */

namespace Mageants\VoiceSearch\ViewModel;

use Mageants\VoiceSearch\Helper\Data;

class VoiceSearch implements \Magento\Framework\View\Element\Block\ArgumentInterface
{
    /**
     * @var Data
     */
    protected $helper;

    /**
     * @param Data $helper
     */
    public function __construct(
        Data $helper
    ) {
        $this->helper = $helper;
    }

    /**
     * GetEnablePreselect
     *
     * @return int
     */
    public function getVoiceSearch()
    {
        return $this->helper->getConvertedLocaleCode();
    }
}
