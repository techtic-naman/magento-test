<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\RewardPoints\Setup\Patch\Data;

use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\Patch\PatchVersionInterface;

class FillTransactionData implements DataPatchInterface, PatchVersionInterface
{
    /**
     * @var \Magento\Framework\Setup\ModuleDataSetupInterface
     */
    private $moduleDataSetup;

    /**
     * @param \Magento\Framework\Setup\ModuleDataSetupInterface $moduleDataSetup
     */
    public function __construct(
        \Magento\Framework\Setup\ModuleDataSetupInterface $moduleDataSetup
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
    }

    /**
     * {@inheritdoc}
     */
    public function apply()
    {
        $this->fillCommentColumn();
        $this->fillIsNeedSaveNotificationColumn();
    }

    private function fillCommentColumn()
    {
        $connection = $this->moduleDataSetup->getConnection();
        $tableName  = $this->moduleDataSetup->getTable('mageworx_rewardpoints_transaction');
        $select     = $connection->select();
        $columns    = ['transaction_id', 'event_data'];
        $select->from($tableName, $columns);
        $data = $connection->fetchAll($select);

        $ids        = [];
        $conditions = [];

        foreach ($data as $datum) {

            if ($datum['event_data']) {

                $result = json_decode($datum['event_data'], true);

                if (!empty($result['comment'])) {

                    $ids[] = $datum['transaction_id'];

                    $conditions[$connection->quoteInto('?', $datum['transaction_id'])] =
                        $connection->quoteInto('?', $result['comment']);
                }
            }
        }

        if ($conditions) {
            $expression = $connection->getCaseSql('transaction_id', $conditions);
            $where      = ['transaction_id IN (?)' => $ids];

            $connection->update(
                $tableName,
                ['comment' => $expression],
                $where
            );
        }
    }

    private function fillIsNeedSaveNotificationColumn()
    {
        $connection = $this->moduleDataSetup->getConnection();
        $tableName  = $this->moduleDataSetup->getTable('mageworx_rewardpoints_transaction');
        $select     = $connection->select();

        $select
            ->from($tableName, ['transaction_id'])
            ->where('is_notification_sent = 1');

        $ids = $connection->fetchCol($select);

        if ($ids) {

            $connection->update(
                $tableName,
                [
                    'is_need_send_notification' => 1
                ],
                ['transaction_id IN (?)' => $ids]
            );
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
        return '1.0.6';
    }

    /**
     * {@inheritdoc}
     */
    public function getAliases()
    {
        return [];
    }
}
