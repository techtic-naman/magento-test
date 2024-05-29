<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace MageWorx\CountdownTimersBase\Block;

use Magento\Framework\Serialize\Serializer\Json as Serializer;
use Magento\Framework\View\Element\Template\Context;
use MageWorx\CountdownTimersBase\Model\CountdownTimerConfigReaderInterface;

class CountdownTimer extends \Magento\Framework\View\Element\Template
{
    /**
     * @var Serializer
     */
    protected $serializer;

    /**
     * CountdownTimer config reader
     *
     * @var CountdownTimerConfigReaderInterface
     */
    private $configReader;

    /**
     * CountdownTimer constructor.
     *
     * @param Context $context
     * @param CountdownTimerConfigReaderInterface $configReader
     * @param Serializer $serializer
     * @param array $data
     */
    public function __construct(
        Context $context,
        CountdownTimerConfigReaderInterface $configReader,
        Serializer $serializer,
        array $data = []
    ) {
        parent::__construct($context, $data);

        $this->configReader = $configReader;
        $this->serializer   = $serializer;
    }

    /*
    * @return bool
    */
    public function canBeDisplayed(): bool
    {
        return $this->configReader->isEnabled() && $this->getProductId();
    }

    /**
     * @return string
     */
    public function getJsonConfig(): string
    {
        return $this->serializer->serialize($this->getConfig());
    }

    /**
     * @return array
     */
    public function getConfig(): array
    {
        return [
            'ajaxUrl'   => $this->getAjaxUrl(),
            'productId' => $this->getProductId()
        ];
    }

    /**
     * @return string
     */
    protected function getAjaxUrl(): string
    {
        return $this->getUrl('mageworx_countdowntimersbase/ajax/getCountdownTimerData');
    }

    /**
     * @return int|null
     */
    protected function getProductId(): ?int
    {
        if ($this->getRequest()->getFullActionName() === 'catalog_product_view') {
            $productId = (int)$this->getRequest()->getParam('id');

            return $productId ?: null;
        }

        return null;
    }
}
