<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace MageWorx\StockStatus\Model\Source;

use MageWorx\StockStatus\Model\Source;

class DisplayOn extends Source
{
    const CATEGORY_PAGE = 'category_page';
    const SEARCH_PAGE   = 'search_page';
    const PRODUCT_PAGE  = 'product_page';

    /**
     * @return array
     */
    public function toOptionArray(): array
    {
        return [
            ['value' => self::PRODUCT_PAGE, 'label' => __('Products')],
            ['value' => self::CATEGORY_PAGE, 'label' => __('Categories')],
            ['value' => self::SEARCH_PAGE, 'label' => __('Search Results')]
        ];
    }
}