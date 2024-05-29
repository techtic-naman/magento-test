<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace MageWorx\SocialProofBase\Block\Campaign;

class VerifiedByMageWorx extends \Magento\Framework\View\Element\Text
{
    /**
     * @return string
     */
    protected function _toHtml(): string
    {
//      Added class "lazyOwl" for compatibility with Amasty_PageSpeedOptimizer
        $html = '<div class="mw-sp__verified"><span class="mw-sp__verified__text">' . __('Verified by') . '</span>' .
            '<img class="mw-sp__verified__logo lazyOwl" alt="MageWorx" aria-label="MageWorx" ' .
            'src="' . $this->getViewFileUrl('MageWorx_SocialProofBase::images/mw.svg') . '"/></div>';

        $this->setText('');
        $this->addText($html);

        return parent::_toHtml();
    }
}
