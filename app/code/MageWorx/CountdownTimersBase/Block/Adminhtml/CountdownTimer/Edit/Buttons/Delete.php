<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace MageWorx\CountdownTimersBase\Block\Adminhtml\CountdownTimer\Edit\Buttons;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;
use MageWorx\CountdownTimersBase\Api\Data\CountdownTimerInterface;
use Magento\Framework\Exception\LocalizedException;

class Delete extends Generic implements ButtonProviderInterface
{
    /**
     * Get button data
     *
     * @return array
     * @throws LocalizedException
     */
    public function getButtonData(): array
    {
        if (!$this->getCountdownTimerId()) {
            return [];
        }

        return [
            'label'      => __('Delete Timer'),
            'class'      => 'delete',
            'on_click'   => 'deleteConfirm(\'' . __('Are you sure you want to do this?') . '\', \'' .
                $this->getDeleteUrl() . '\')',
            'sort_order' => 20,
        ];
    }

    /**
     * @return string
     * @throws LocalizedException
     */
    public function getDeleteUrl(): string
    {
        return $this->getUrl(
            '*/*/delete',
            [CountdownTimerInterface::COUNTDOWN_TIMER_ID => $this->getCountdownTimerId()]
        );
    }
}
