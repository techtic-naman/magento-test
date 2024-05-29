<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace MageWorx\CountdownTimersBase\Block\Widget;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Serialize\Serializer\Json as Serializer;
use Magento\Framework\View\Element\Template\Context;
use Magento\Widget\Block\BlockInterface;
use MageWorx\CountdownTimersBase\Api\Data\CountdownTimerInterface;
use MageWorx\CountdownTimersBase\Api\CountdownTimerRepositoryInterface;
use MageWorx\CountdownTimersBase\Model\CountdownTimerConfigReaderInterface;

class CountdownTimer extends \Magento\Framework\View\Element\Template implements BlockInterface
{
    /**
     * @var Serializer
     */
    protected $serializer;

    /**
     * @var CountdownTimerRepositoryInterface
     */
    protected $countdownTimerRepository;

    /**
     * CountdownTimer config reader
     *
     * @var CountdownTimerConfigReaderInterface
     */
    private $configReader;

    /**
     * @var CountdownTimerInterface
     */
    private $countdownTimer;

    /**
     * CountdownTimer constructor.
     *
     * @param Context $context
     * @param CountdownTimerRepositoryInterface $countdownTimerRepository
     * @param CountdownTimerConfigReaderInterface $configReader
     * @param Serializer $serializer
     * @param array $data
     */
    public function __construct(
        Context $context,
        CountdownTimerRepositoryInterface $countdownTimerRepository,
        CountdownTimerConfigReaderInterface $configReader,
        Serializer $serializer,
        array $data = []
    ) {
        parent::__construct($context, $data);

        $this->countdownTimerRepository = $countdownTimerRepository;
        $this->configReader             = $configReader;
        $this->serializer               = $serializer;
    }

    /*
    * @return bool
    */
    public function canBeDisplayed(): bool
    {
        return $this->configReader->isEnabled()
            && $this->getCountdownTimer();
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
            'ajaxUrl'          => $this->getAjaxUrl(),
            'countdownTimerId' => $this->getCountdownTimer()->getId()
        ];
    }

    /**
     * @return string
     */
    protected function getAjaxUrl(): string
    {
        return $this->getUrl('mageworx_countdowntimersbase/ajax_widget/getCountdownTimerData');
    }

    /**
     * Get Countdown Timer
     *
     * @return CountdownTimerInterface|null
     */
    private function getCountdownTimer(): ?CountdownTimerInterface
    {
        if ($this->countdownTimer) {
            return $this->countdownTimer;
        }

        $countdownTimerId = $this->getData('countdown_timer_id');

        if ($countdownTimerId) {
            try {
                $countdownTimer       = $this->countdownTimerRepository->getById((int)$countdownTimerId);
                $this->countdownTimer = $countdownTimer;

                return $countdownTimer;
            } catch (NoSuchEntityException $e) {
            }
        }

        return null;
    }
}
