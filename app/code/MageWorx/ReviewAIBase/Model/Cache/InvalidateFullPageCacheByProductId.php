<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types = 1);

namespace MageWorx\ReviewAIBase\Model\Cache;

use Magento\Framework\App\Cache\TypeListInterface as CacheTypeList;
use Magento\Framework\Event\ManagerInterface as EventManager;
use MageWorx\ReviewAIBase\Api\InvalidateFullPageCacheByProductIdInterface;

class InvalidateFullPageCacheByProductId implements InvalidateFullPageCacheByProductIdInterface
{
    protected CacheTypeList $cacheTypeList;
    protected EventManager  $eventManager;

    /**
     * Constructor
     *
     * @param CacheTypeList $cacheTypeList
     * @param EventManager $eventManager
     */
    public function __construct(
        CacheTypeList $cacheTypeList,
        EventManager  $eventManager
    ) {
        $this->cacheTypeList = $cacheTypeList;
        $this->eventManager  = $eventManager;
    }

    /**
     * Invalidate the FPC for a specific product by its ID.
     *
     * @param int $productId
     */
    public function invalidateProductFpc(int $productId): void
    {
        $cacheTags = [
            \Magento\Catalog\Model\Product::CACHE_TAG . '_' . $productId,
            'mageworx_reviewai_product_review_summary_' . $productId
        ];
        $this->eventManager->dispatch('clean_cache_by_tags', ['tags' => $cacheTags]);
    }
}
