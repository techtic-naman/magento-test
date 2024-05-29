<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace MageWorx\SocialProofBase\Block\Adminhtml\Campaign\StepsWizard;

class Wrapper extends \Magento\Backend\Block\Template
{
    /**
     * Return Steps Wizard.
     *
     * @param array $initData
     * @return string
     */
    public function getStepsWizard($initData = []): string
    {
        /** @var \Magento\Ui\Block\Component\StepsWizard $block */
        $block = $this->getChildBlock($this->getData('config/stepsWizardName'));

        if ($block) {
            $block->setInitData($initData);

            return $block->toHtml();
        }

        return '';
    }

    /**
     * Can show tab in tabs
     *
     * @return boolean
     */
    public function canShowTab(): bool
    {
        return true;
    }

    /**
     * Tab should be loaded trough Ajax call
     *
     * @return bool
     */
    public function isAjaxLoaded(): bool
    {
        return false;
    }
}
