<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\RewardPoints\Setup\Patch\Data;

use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\Patch\PatchVersionInterface;

class UpdateConfigSettings implements DataPatchInterface, PatchVersionInterface
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
        $connection = $this->moduleDataSetup->getConnection();

        $connection->update(
            $this->moduleDataSetup->getTable('core_config_data'),
            ['path' => 'mageworx_rewardpoints/marketing/display_upcoming_points_message'],
            "path = 'mageworx_rewardpoints/main/display_upcoming_points_message'"
        );

        $connection->update(
            $this->moduleDataSetup->getTable('core_config_data'),
            ['path' => 'mageworx_rewardpoints/marketing/upcoming_points_message'],
            "path = 'mageworx_rewardpoints/main/upcoming_points_message'"
        );

        $connection->update(
            $this->moduleDataSetup->getTable('core_config_data'),
            ['path' => 'mageworx_rewardpoints/marketing/display_minicart_point_balance_message'],
            "path = 'mageworx_rewardpoints/main/display_minicart_point_balance_message'"
        );

        $connection->update(
            $this->moduleDataSetup->getTable('core_config_data'),
            ['path' => 'mageworx_rewardpoints/marketing/minicart_point_balance_message'],
            "path = 'mageworx_rewardpoints/main/minicart_point_balance_message'"
        );

        $connection->update(
            $this->moduleDataSetup->getTable('core_config_data'),
            ['path' => 'mageworx_rewardpoints/marketing/minicart_empty_point_balance_message'],
            "path = 'mageworx_rewardpoints/main/minicart_empty_point_balance_message'"
        );

        $connection->update(
            $this->moduleDataSetup->getTable('core_config_data'),
            ['path' => 'mageworx_rewardpoints/marketing/rss_enable'],
            "path = 'mageworx_rewardpoints/main/rss_enable'"
        );
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
    public function getAliases()
    {
        return [];
    }

    public static function getVersion()
    {
        return '1.0.5';
    }
}
