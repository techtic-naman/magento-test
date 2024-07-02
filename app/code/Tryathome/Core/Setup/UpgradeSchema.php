<?php
namespace Tryathome\Core\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\App\ObjectManager;

class UpgradeSchema implements UpgradeSchemaInterface
{
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();
        $setup->startSetup();

        $objectManager = ObjectManager::getInstance();
        $logger = $objectManager->get(\Psr\Log\LoggerInterface::class);
        $logger->info('over here');

        if (version_compare($context->getVersion(), '1.0.7', '<')) {
            $logger->info('Upgrading to version 1.0.7');
            // Create table 'tryathome_try' if it doesn't exist
            if (!$installer->tableExists('tryathome_try')) {
                $logger->info('its inside it.');
                $table = $installer->getConnection()->newTable(
                    $installer->getTable('tryathome_try')
                )->addColumn(
                    'try_id',
                    Table::TYPE_INTEGER,
                    null,
                    ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                    'Try ID'
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
                )->addColumn(
                    'name',
                    Table::TYPE_TEXT,
                    255,
                    ['nullable' => false],
                    'Name'
                )->addColumn(
                    'email',
                    Table::TYPE_TEXT,
                    255,
                    ['nullable' => false],
                    'email'
                )
                ->addColumn(
                    'house_no',
                    Table::TYPE_TEXT,
                    '2M',
                    ['nullable' => false],
                    'House Number'
                )
                ->addColumn(
                    'city',
                    Table::TYPE_TEXT,
                    255,
                    ['nullable' => false],
                    'City'
                )
                ->addColumn(
                    'pincode',
                    Table::TYPE_INTEGER,
                    null,
                    ['nullable' => false, 'unsigned' => true],
                    'Pincode'
                )->addColumn(
                    'state',
                    Table::TYPE_TEXT,
                    255,
                    ['nullable' => false],
                    'State'
                )
                ->addColumn(
                    'country_code',
                    Table::TYPE_TEXT,
                    3,
                    ['nullable' => false],
                    'Country Code'
                )
                ->addColumn(
                    'phone_number',
                    Table::TYPE_TEXT,
                    255,
                    ['nullable' => false],
                    'Phone Number'
                )->setComment(
                    'Try at Home'
                );
                $installer->getConnection()->createTable($table);
            } else {
                $logger->info('Table already exists');
            }
            if (!$installer->tableExists('trial_product_user')) {
                $table = $installer->getConnection()->newTable(
                    $installer->getTable('trial_product_user')
                )->addColumn(
                    'trial_id',
                    Table::TYPE_INTEGER,
                    null,
                    ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                    'Trial ID'
                )
                ->addColumn(
                    'try_id',
                    Table::TYPE_INTEGER,
                    null,
                    ['unsigned' => true, 'nullable' => false],
                    'Try ID from tryathome_try table'
                )
                ->addColumn(
                    'product_id',
                    Table::TYPE_INTEGER,
                    null,
                    ['unsigned' => true, 'nullable' => true],
                    'Product ID'
                )
                ->addColumn(
                    'seller_id',
                    Table::TYPE_INTEGER,
                    null,
                    ['unsigned' => true, 'nullable' => true],
                    'Seller ID'
                )
                ->addForeignKey(
                    $setup->getFkName('trial_product_user', 'try_id', 'tryathome_try', 'try_id'),
                    'try_id',
                    $setup->getTable('tryathome_try'),
                    'try_id',
                    Table::ACTION_CASCADE
                )
                ->addForeignKey(
                        $setup->getFkName('trial_product_user', 'product_id', 'catalog_product_entity', 'entity_id'), // Assuming 'products' table and 'id' as PK
                        'product_id',
                        $setup->getTable('catalog_product_entity'), // Adjust if your products table has a different name
                        'entity_id',
                        Table::ACTION_SET_NULL
                )
                ->setComment(
                    'Trial Product User Table'
                );
                $installer->getConnection()->createTable($table);
            }
        } else {
            $logger->info('Not found');
        }

        $installer->endSetup();
        $logger->info('UpgradeSchema script ended');
    }
}