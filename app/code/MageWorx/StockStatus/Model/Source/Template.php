<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace MageWorx\StockStatus\Model\Source;

use MageWorx\StockStatus\Model\Source;

class Template extends Source
{
    const DEFAULT = 'default';

    /**
     * @return array
     */
    public function toOptionArray(): array
    {
        return [
            ['value' => self::DEFAULT, 'label' => __('Default template')]
        ];
    }
}