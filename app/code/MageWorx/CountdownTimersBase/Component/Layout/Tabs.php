<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace MageWorx\CountdownTimersBase\Component\Layout;

class Tabs extends \Magento\Ui\Component\Layout\Tabs
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
        $block->setTemplate('Magento_Ui::layout/tabs/nav/default.phtml');

        $this->component->getContext()->addComponentDefinition(
            'nav',
            [
                'component' => 'MageWorx_CountdownTimersBase/js/countdown-timer/form/components/tab_group',
                'config'    => [
                    'template' => 'MageWorx_CountdownTimersBase/tab'
                ],
                'extends'   => $this->namespace
            ]
        );
    }
}
