<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace MageWorx\CountdownTimersBase\Block\Adminhtml\CountdownTimer\Edit\Buttons;

use MageWorx\CountdownTimersBase\Api\CountdownTimerRepositoryInterface;
use Magento\Backend\Block\Widget\Context;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use MageWorx\CountdownTimersBase\Api\Data\CountdownTimerInterface;

class Generic
{
    /**
     * Widget Context
     *
     * @var Context
     */
    protected $context;

    /**
     * Countdown Timer Repository
     *
     * @var CountdownTimerRepositoryInterface
     */
    protected $countdownTimerRepository;

    /**
     * constructor
     *
     * @param Context $context
     * @param CountdownTimerRepositoryInterface $countdownTimerRepository
     */
    public function __construct(
        Context $context,
        CountdownTimerRepositoryInterface $countdownTimerRepository
    ) {
        $this->context                  = $context;
        $this->countdownTimerRepository = $countdownTimerRepository;
    }

    /**
     * Return Countdown Timer ID
     *
     * @return string|null
     * @throws LocalizedException
     */
    public function getCountdownTimerId(): ?string
    {
        try {
            $id = $this->context->getRequest()->getParam(CountdownTimerInterface::COUNTDOWN_TIMER_ID);

            return $this->countdownTimerRepository->getById((int)$id)->getId();
        } catch (NoSuchEntityException $e) {
            return null;
        }
    }

    /**
     * Generate url by route and parameters
     *
     * @param string $route
     * @param array $params
     * @return  string
     */
    public function getUrl($route = '', array $params = []): string
    {
        return $this->context->getUrlBuilder()->getUrl($route, $params);
    }
}
