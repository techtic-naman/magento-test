<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace MageWorx\SocialProofBase\Component\Layout;

class HiddenTabs extends \Magento\Ui\Component\Layout\Tabs
{
    /**
     * Add navigation block
     *
     * @return void
     */
    protected function addNavigationBlock(): void
    {
        $blockName  = 'tabs_nav';
        $pageLayout = $this->component->getContext()->getPageLayout();

        if ($pageLayout->hasElement($blockName)) {
            $blockName = $this->component->getName() . '_tabs_nav';
        }

        /** @var \Magento\Ui\Component\Layout\Tabs\Nav $block */
        if (isset($this->navContainerName)) {
            $block = $pageLayout->addBlock(
                \Magento\Ui\Component\Layout\Tabs\Nav::class,
                $blockName,
                $this->navContainerName
            );
        } else {
            $block = $pageLayout->addBlock(\Magento\Ui\Component\Layout\Tabs\Nav::class, $blockName, 'content');
        }

        $block->setData('data_scope', $this->namespace);
        $block->setTemplate('MageWorx_SocialProofBase::layout/tabs/hidden-tabs.phtml');

        $this->component->getContext()->addComponentDefinition(
            'nav',
            [
                'component' => 'MageWorx_SocialProofBase/js/campaign/form/components/tab_group',
                'config'    => [
                    'template' => 'MageWorx_SocialProofBase/tab'
                ],
                'extends'   => $this->namespace
            ]
        );
    }
}
