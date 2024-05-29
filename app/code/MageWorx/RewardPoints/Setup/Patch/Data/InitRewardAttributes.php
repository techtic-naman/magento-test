<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\RewardPoints\Setup\Patch\Data;

use Magento\Quote\Setup\QuoteSetupFactory;
use Magento\Sales\Setup\SalesSetupFactory;
use Magento\Framework\Setup\Patch\PatchVersionInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

class InitRewardAttributes implements DataPatchInterface, PatchVersionInterface
{
    /**
     * @var ModuleDataSetupInterface $moduleDataSetup
     */
    private $moduleDataSetup;

    /**
     * @var SalesSetupFactory
     */
    private $salesSetupFactory;

    /**
     * @var QuoteSetupFactory
     */
    private $quoteSetupFactory;

    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        SalesSetupFactory $salesSetupFactory,
        QuoteSetupFactory $quoteSetupFactory
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->salesSetupFactory = $salesSetupFactory;
        $this->quoteSetupFactory = $quoteSetupFactory;
    }

    public function apply()
    {
        $quoteInstaller = $this->quoteSetupFactory->create(
            ['resourceName' => 'quote_setup', 'setup' => $this->moduleDataSetup]
        );
        $salesInstaller = $this->salesSetupFactory->create(
            ['resourceName' => 'sales_setup', 'setup' => $this->moduleDataSetup]
        );

        //An attribute code must not be less than 1 and more than 30 characters

        $quoteInstaller->addAttribute(
            'quote',
            'mw_rwrdpoints_amnt',
            ['type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL]
        );
        $quoteInstaller->addAttribute(
            'quote',
            'base_mw_rwrdpoints_cur_amnt',
            ['type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL]
        );
        $quoteInstaller->addAttribute(
            'quote',
            'mw_rwrdpoints_cur_amnt',
            ['type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL]
        );

        $quoteInstaller->addAttribute(
            'quote',
            'use_mw_reward_points',
            ['type' => \Magento\Framework\DB\Ddl\Table::TYPE_BOOLEAN]
        );

        $quoteInstaller->addAttribute(
            'order',
            'mw_rwrdpoints_amnt_refunded',
            ['type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL]
        );


        $quoteInstaller->addAttribute(
            'quote_address',
            'mw_rwrdpoints_amnt',
            ['type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL]
        );
        $quoteInstaller->addAttribute(
            'quote_address',
            'base_mw_rwrdpoints_cur_amnt',
            ['type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL]
        );
        $quoteInstaller->addAttribute(
            'quote_address',
            'mw_rwrdpoints_cur_amnt',
            ['type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL]
        );

        $salesInstaller->addAttribute(
            'order',
            'mw_rwrdpoints_amnt',
            ['type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL]
        );
        $salesInstaller->addAttribute(
            'order',
            'base_mw_rwrdpoints_cur_amnt',
            ['type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL]
        );
        $salesInstaller->addAttribute(
            'order',
            'mw_rwrdpoints_cur_amnt',
            ['type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL]
        );
        $salesInstaller->addAttribute(
            'order',
            'base_mw_rwrdpoints_cur_amnt_invoice',
            ['type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL]
        );
        $salesInstaller->addAttribute(
            'order',
            'mw_rwrdpoints_cur_amnt_invoice',
            ['type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL]
        );
        $salesInstaller->addAttribute(
            'order',
            'base_mw_rwrdpoints_cur_amnt_refund',
            ['type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL]
        );

        $salesInstaller->addAttribute(
            'order',
            'mw_rwrdpoints_cur_amnt_refund',
            ['type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL]
        );

        $salesInstaller->addAttribute(
            'order',
            'mw_rwrdpoints_amnt_refund',
            ['type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL]
        );

        $salesInstaller->addAttribute(
            'invoice',
            'base_mw_rwrdpoints_cur_amnt',
            ['type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL]
        );
        $salesInstaller->addAttribute(
            'invoice',
            'mw_rwrdpoints_cur_amnt',
            ['type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL]
        );

        $salesInstaller->addAttribute(
            'invoice',
            'mw_rwrdpoints_amnt',
            ['type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL]
        );

        $salesInstaller->addAttribute(
            'creditmemo',
            'base_mw_rwrdpoints_cur_amnt',
            ['type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL]
        );
        $salesInstaller->addAttribute(
            'creditmemo',
            'mw_rwrdpoints_cur_amnt',
            ['type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL]
        );

        $salesInstaller->addAttribute(
            'creditmemo',
            'mw_rwrdpoints_amnt',
            ['type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL]
        );


        $salesInstaller->addAttribute(
            'creditmemo',
            'mw_rwrdpoints_amnt_refund',
            ['type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getAliases()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public static function getDependencies()
    {
        return [

        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function getVersion()
    {
        return '1.0.0';
    }
}
