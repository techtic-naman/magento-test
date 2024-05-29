<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace MageWorx\StockStatus\Block\ProductList;

use Magento\Framework\View\Element\Template;
use MageWorx\StockStatus\Model\Source\DisplayOn;
use MageWorx\StockStatus\Model\StockStatusConfigReader;

class StockStatus extends \Magento\Framework\View\Element\Template
{
    /**
     * @var StockStatusConfigReader
     */
    protected $stockStatusConfigReader;

    /**
     * StockStatus constructor.
     *
     * @param StockStatusConfigReader $stockStatusConfigReader
     * @param Template\Context $context
     * @param array $data
     */
    public function __construct(
        StockStatusConfigReader $stockStatusConfigReader,
        Template\Context $context,
        array $data = []
    )
    {
        $this->stockStatusConfigReader = $stockStatusConfigReader;
        parent::__construct($context, $data);
    }

    /**
     * @return string
     */
    public function getAjaxUrl(): string
    {
        return $this->getUrl('mageworx_stock_status/stock/massUpdate');
    }

    /**
     * @return bool
     */
    public function isEnabled(): bool
    {
        if ($this->_request->getFullActionName() === 'catalog_category_view' &&
            array_search(DisplayOn::CATEGORY_PAGE, $this->stockStatusConfigReader->getDisplayOn()) === false
        ) {
            return false;
        }

        return true;
    }
}
