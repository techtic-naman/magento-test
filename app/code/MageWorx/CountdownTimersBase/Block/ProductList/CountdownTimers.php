<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace MageWorx\CountdownTimersBase\Block\ProductList;

use Magento\Framework\View\Element\Template\Context;
use MageWorx\CountdownTimersBase\Model\CountdownTimerConfigReaderInterface;

class CountdownTimers extends \Magento\Framework\View\Element\Template
{
    /**
     * CountdownTimer config reader
     *
     * @var CountdownTimerConfigReaderInterface
     */
    private $configReader;

    /**
     * CountdownTimers constructor.
     *
     * @param Context $context
     * @param CountdownTimerConfigReaderInterface $configReader
     * @param array $data
     */
    public function __construct(
        Context $context,
        CountdownTimerConfigReaderInterface $configReader,
        array $data = []
    ) {
        parent::__construct($context, $data);

        $this->configReader = $configReader;
    }

    /*
    * @return bool
    */
    public function canBeDisplayed(): bool
    {
        return $this->configReader->isEnabled();
    }

    /**
     * @return string
     */
    public function getAjaxUrl(): string
    {
        return $this->getUrl('mageworx_countdowntimersbase/ajax_productList/getCountdownTimersData');
    }
}
