<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types = 1);

namespace MageWorx\ReviewAIBase\Model\AdditionalDataProcessor;

use Magento\Framework\App\ResourceConnection;
use Magento\Review\Model\Review;
use MageWorx\ReviewAIBase\Api\AdditionalDataProcessorInterface;

class IsHelpful implements AdditionalDataProcessorInterface
{
    protected ResourceConnection $resourceConnection;

    public function __construct(
        ResourceConnection $resourceConnection
    ) {
        $this->resourceConnection = $resourceConnection;
    }

    /**
     * @inheritDoc
     */
    public function process(Review $review): ?string
    {
        $reviewId   = $review->getId();
        $connection = $this->resourceConnection->getConnection();

        // Safely check if table exists
        $tableName = $connection->getTableName('mageworx_xreviewbase_review_vote');
        if ($connection->isTableExists($tableName)) {
            $select = $connection->select()
                                 ->from(
                                     $tableName,
                                     [
                                         new \Zend_Db_Expr(
                                             'IF(likes_count > dislikes_count, 1,
                                                IF(dislikes_count > likes_count, -1, 0)) AS recommendation'
                                         )
                                     ]
                                 )
                                 ->where('review_id = ?', $reviewId);

            $result = $connection->fetchOne($select);

            if ($result === '1') {
                return 'Yes';
            } elseif ($result === '-1') {
                return 'No';
            } else {
                // Return an empty string or a different message if likes and dislikes are equal or there are no votes
                return 'Not Defined';
            }
        }

        return '';
    }
}
