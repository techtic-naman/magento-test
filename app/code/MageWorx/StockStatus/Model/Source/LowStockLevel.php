<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace MageWorx\StockStatus\Model\Source;

use MageWorx\StockStatus\Model\Source;

class LowStockLevel extends Source
{
    const DEFAULT = 'default';
    const CUSTOM  = 'custom';

    /**
     * @return array
     */
    public function toOptionArray(): array
    {
        return [
            ['value' => self::DEFAULT, 'label' => __('Use "Notify for Quantity Below" setting')],
            ['value' => self::CUSTOM, 'label' => __('Custom "Low Stock" value.')]
        ];
    }
}