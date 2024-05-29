<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types = 1);

namespace MageWorx\ReviewAIBase\Model\ResourceModel;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use MageWorx\ReviewAIBase\Api\Data\ReviewSummaryInterface;
use MageWorx\ReviewAIBase\Model\Config\Source\Status;

class ReviewSummary extends AbstractDb
{
    protected function _construct()
    {
        $this->_init('mageworx_reviewai_review_summary', 'entity_id');
    }

    /**
     * @param array $productIds
     * @param int $storeId
     * @return int
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function massTriggerUpdate(array $productIds, int $storeId): int
    {
        $connection = $this->getConnection();
        $tableName  = $this->getMainTable();

        $where = [
            'product_id IN(?)' => $productIds,
            'store_id = ?'     => $storeId
        ];

        $data =
            [ReviewSummaryInterface::UPDATE_REQUIRED => true, ReviewSummaryInterface::STATUS => Status::STATUS_PENDING];

        return $connection->update($tableName, $data, $where);
    }

    /**
     * @param array $productIds
     * @param int $storeId
     * @param bool $isEnabled
     * @return int
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function massSetIsEnabled(array $productIds, int $storeId, bool $isEnabled): int
    {
        $connection = $this->getConnection();
        $tableName  = $this->getMainTable();

        $where = [
            'product_id IN(?)' => $productIds,
            'store_id = ?'     => $storeId
        ];

        $data = [ReviewSummaryInterface::IS_ENABLED => $isEnabled];
        if (!$isEnabled) {
            $data[ReviewSummaryInterface::STATUS] = Status::STATUS_DISABLED;
        } else {
            $data[ReviewSummaryInterface::STATUS] = Status::STATUS_PENDING;
        }

        return $connection->update($tableName, $data, $where);
    }

    /**
     * @param array $productIds
     * @param int $storeId
     * @return int
     * @throws LocalizedException
     */
    public function massDisable(array $productIds, int $storeId): int
    {
        return $this->massSetIsEnabled($productIds, $storeId, false);
    }

    /**
     * @param array $productIds
     * @param int $storeId
     * @return int
     * @throws LocalizedException
     */
    public function massEnable(array $productIds, int $storeId): int
    {
        return $this->massSetIsEnabled($productIds, $storeId, true);
    }

    /**
     * Mass update status of records by provided product id and store id
     *
     * @param array $productIds
     * @param int $storeId
     * @param int $status
     * @return int Number of affected rows
     * @throws LocalizedException
     */
    public function massUpdateStatus(array $productIds, int $storeId, int $status): int
    {
        $connection = $this->getConnection();
        $tableName  = $this->getMainTable();

        $where = [
            'product_id IN(?)' => $productIds,
            'store_id = ?'     => $storeId
        ];

        $data = ['status' => $status];

        return $connection->update($tableName, $data, $where);
    }

    /**
     * Mass insert data to array by combined key product_id and store_id.
     * $data - any column with value, to insert, like:
     * [
     *     'status' => 2,
     *     'content' => ''
     * ]
     * Returns number of rows which successfully has been inserted.
     *
     * @param array $productIds
     * @param int $storeId
     * @param array $data
     * @return int
     * @throws LocalizedException
     */
    public function massInsertOnDuplicateKeyIgnore(array $productIds, int $storeId, array $data): int
    {
        if (empty($productIds) || empty($data)) {
            // Handle the case where there is no data to insert.
            return 0;
        }

        $connection = $this->getConnection();
        $tableName  = $this->getMainTable();

        $insertData = [];
        foreach ($productIds as $productId) {
            $insertData[] = array_merge(['product_id' => $productId, 'store_id' => $storeId], $data);
        }

        $columns = array_keys($insertData[0]);
        $columnsList = implode(', ', $columns);

        $valuesPlaceholder = [];
        $bind = [];
        foreach ($insertData as $index => $row) {
            $placeholders = [];
            foreach ($row as $column => $value) {
                $bindKey = ":{$column}_{$index}";
                $placeholders[] = $bindKey;
                $bind[$bindKey] = $value;
            }
            $valuesPlaceholder[] = '(' . implode(', ', $placeholders) . ')';
        }

        $valuesList = implode(', ', $valuesPlaceholder);
        $sql = "INSERT IGNORE INTO $tableName ($columnsList) VALUES $valuesList";

        $stmt = $connection->prepare($sql);
        $stmt->execute($bind);

        return $stmt->rowCount();
    }

}
