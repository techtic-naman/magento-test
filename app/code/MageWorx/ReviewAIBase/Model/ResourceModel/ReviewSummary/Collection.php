<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace MageWorx\ReviewAIBase\Model\ResourceModel\ReviewSummary;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    protected function _construct()
    {
        $this->_init(
            \MageWorx\ReviewAIBase\Model\ReviewSummary::class,
            \MageWorx\ReviewAIBase\Model\ResourceModel\ReviewSummary::class
        );
    }
}
