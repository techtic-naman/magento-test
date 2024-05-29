<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace MageWorx\SocialProofBase\Block\Campaign;

use Magento\Framework\Exception\LocalizedException;
use MageWorx\SocialProofBase\Model\Source\Campaign\DisplayMode as DisplayModeOptions;

class Popup extends \MageWorx\SocialProofBase\Block\Campaign
{
    /**
     * @var int
     */
    protected $displayMode = DisplayModeOptions::POPUP;

    /**
     * @return array
     * @throws LocalizedException
     */
    public function getConfig(): array
    {
        $config                 = parent::getConfig();
        $config['verifiedHtml'] = $this->getVerifiedHtml();

        return $config;
    }

    /**
     * @return string
     * @throws LocalizedException
     */
    protected function getVerifiedHtml(): string
    {
        $block = $this->getLayout()->getBlock('mageworx_socialproofbase_campaign_popup_verified_html');

        if (!$block) {
            $block = $this->getLayout()->createBlock(
                \MageWorx\SocialProofBase\Block\Campaign\VerifiedByMageWorx::class,
                'mageworx_socialproofbase_campaign_popup_verified_html'
            );
        }

        return $block->toHtml();
    }
}
