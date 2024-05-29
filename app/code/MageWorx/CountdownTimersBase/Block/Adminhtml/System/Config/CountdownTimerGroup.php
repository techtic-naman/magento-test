<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace MageWorx\CountdownTimersBase\Block\Adminhtml\System\Config;

use Magento\Framework\Data\Form\Element\AbstractElement;

class CountdownTimerGroup extends \Magento\Config\Block\System\Config\Form\Fieldset
{
    /**
     * Render fieldset html
     *
     * @param AbstractElement $element
     * @return string
     */
    public function render(AbstractElement $element): string
    {
        $link    = $this->getUrl('mageworx_countdowntimersbase/countdownTimer/');
        $comment = __("This setting allows you to enable or disable the countdown timers globally on the front-end.");
        $comment .= " " . __(
                "To add or modify the timers, go to Marketing - %link.",
                ['link' => '<a target="_blank" href="' . $link . '">' . __('Countdown Timers') . '</a>']
            );

        $element->setComment($comment);

        return parent::render($element);
    }
}
