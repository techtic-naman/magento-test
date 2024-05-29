<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\CountdownTimersBase\Setup\Patch\Data;

use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\Patch\PatchVersionInterface;
use MageWorx\CountdownTimersBase\Model\CountdownTimer\Template as CountdownTimerTemplate;
use MageWorx\CountdownTimersBase\Model\ResourceModel\CountdownTimer;

class AddTemplates implements DataPatchInterface, PatchVersionInterface
{
    /**
     * @var ModuleDataSetupInterface
     */
    private $moduleDataSetup;

    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
    }

    /**
     * {@inheritdoc}
     */
    public function apply()
    {
        $table  = $this->moduleDataSetup->getTable(CountdownTimer::COUNTDOWN_TIMER_TEMPLATE_TABLE);
        $select = $this->moduleDataSetup->getConnection()->select();
        $select->from($table, CountdownTimerTemplate::IDENTIFIER);

        $identifiers = $this->moduleDataSetup->getConnection()->fetchCol($select);
        $templates   = $this->getTemplates();

        foreach ($templates as $key => $template) {
            if (in_array($template[CountdownTimerTemplate::IDENTIFIER], $identifiers)) {
                unset($templates[$key]);
            }
        }

        if (!empty($templates)) {
            $this->moduleDataSetup->getConnection()->insertMultiple($table, $templates);
        }
    }

    /**
     * {@inheritdoc}
     */
    public static function getDependencies()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public static function getVersion()
    {
        return '1.0.0';
    }

    /**
     * {@inheritdoc}
     */
    public function getAliases()
    {
        return [];
    }

    /**
     * @return array
     */
    protected function getTemplates(): array
    {
        return [
            $this->getTemplateData(1, 'roll', 1),
            $this->getTemplateData(2, 'roll', 2),
            $this->getTemplateData(3, 'roll', 3),
            $this->getTemplateData(4, 'roll', 4),
            $this->getTemplateData(5, 'flip_single', 1),
            $this->getTemplateData(6, 'flip_single', 2),
            $this->getTemplateData(7, 'flip_single', 3),
            $this->getTemplateData(8, 'flip_single', 4),
            $this->getTemplateData(9, 'flip_double', 1),
            $this->getTemplateData(10, 'flip_double', 2),
            $this->getTemplateData(11, 'flip_double', 3),
            $this->getTemplateData(12, 'flip_double', 4),
            $this->getTemplateData(13, 'tick', 3),
            $this->getTemplateData(14, 'tick', 1),
            $this->getTemplateData(15, 'tick', 2),
            $this->getTemplateData(16, 'tick', 4),
            $this->getTemplateData(17, 'plain', 1),
            $this->getTemplateData(18, 'inline', 1),
            $this->getTemplateData(19, 'inline', 2),
            $this->getTemplateData(20, 'inline', 3),
            $this->getTemplateData(21, 'inline', 4),
            $this->getTemplateData(22, 'inline', 5)
        ];
    }

    /**
     * @param int $id
     * @param string $theme
     * @param int $accent
     * @return array
     */
    protected function getTemplateData($id, $theme, $accent): array
    {
        return [
            CountdownTimerTemplate::IDENTIFIER => $theme . '-' . $accent,
            CountdownTimerTemplate::TITLE      => '#' . $id,
            CountdownTimerTemplate::THEME      => $theme,
            CountdownTimerTemplate::ACCENT     => $accent
        ];
    }
}
