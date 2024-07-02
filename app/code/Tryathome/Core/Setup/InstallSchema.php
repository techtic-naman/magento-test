<?php
namespace Tryathome\Core\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

class InstallSchema implements InstallSchemaInterface
{
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();

        // Create table 'tryathome_try'
        $table = $installer->getConnection()->newTable(
            $installer->getTable('tryathome_try')
        )->addColumn(
            'try_id',
            Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'Try ID'
        )->addColumn(
           'product_ids',
            Table::TYPE_TEXT,
            '2M',
            ['nullable' => false],
            'Product IDs in JSON'
        )->addColumn(
            'seller_id',
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false],
            'Seller ID'
        )->addColumn(
            'user_id',
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false],
            'User ID'
        )->addColumn(
            'try_date',
            Table::TYPE_DATETIME,
            null,
            ['nullable' => false],
            'Try Date'
        )->setComment(
            'Try at Home'
        );
        $installer->getConnection()->createTable($table);

        $installer->endSetup();
    }
}