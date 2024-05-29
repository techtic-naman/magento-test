<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace MageWorx\SocialProofBase\Block\Adminhtml\System\Config;

use Magento\Framework\Data\Form\Element\AbstractElement;

class CampaignGroup extends \Magento\Config\Block\System\Config\Form\Fieldset
{
    /**
     * Render fieldset html
     *
     * @param AbstractElement $element
     * @return string
     */
    public function render(AbstractElement $element): string
    {
        $link    = $this->getUrl('mageworx_socialproofbase/campaign/');
        $comment = __(
            "To add or modify the campaigns for the sales pop-up, go to Marketing - %link.",
            ['link' => '<a target="_blank" href="' . $link . '">' . __('Social proof') . '</a>']
        );

        $element->setComment($comment);

        return parent::render($element);
    }
}
