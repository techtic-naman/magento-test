<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types = 1);

namespace MageWorx\ReviewAIBase\Api;

use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\LocalizedException;
use MageWorx\OpenAI\Api\Data\QueueItemInterface;

interface ReviewSummaryGeneratorInterface
{
    /**
     * Generate review summary for a product
     *
     * @param int $productId
     * @param int $storeId
     * @return string
     */
    public function generate(int $productId, int $storeId): string;

    /**
     * Schedule review summary generation using queue
     * Returns array of queue items
     *
     * @param int $productId
     * @param int $storeId
     * @return array|QueueItemInterface[]
     * @throws InputException
     * @throws LocalizedException
     */
    public function addToQueue(int $productId, int $storeId): array;
}
