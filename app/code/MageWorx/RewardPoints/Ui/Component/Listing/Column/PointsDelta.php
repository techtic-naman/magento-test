<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\RewardPoints\Ui\Component\Listing\Column;

use Magento\Ui\Component\Listing\Columns\Column;

class PointsDelta extends Column
{
    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        $dataSource = parent::prepareDataSource($dataSource);

        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                $item['points_delta'] = $item['points_delta'] > 0 ? '+' . $item['points_delta'] : $item['points_delta'];
            }
        }

        return $dataSource;
    }
}