<?php
/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_Marketplace
 * @author    Webkul Software Private Limited
 * @copyright Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */
namespace Webkul\Marketplace\Ui\Component\Listing\Columns\Frontend;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Catalog\Api\ProductRepositoryInterface;

class Status extends Column
{
    /**
     * @var ProductRepositoryInterface
     */
    public $productRepository;

    /**
     * Constructor.
     *
     * @param ContextInterface           $context
     * @param UiComponentFactory         $uiComponentFactory
     * @param ProductRepositoryInterface $productRepository
     * @param array                      $components
     * @param array                      $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        ProductRepositoryInterface $productRepository,
        array $components = [],
        array $data = []
    ) {
        $this->productRepository = $productRepository;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare Data Source.
     *
     * @param array $dataSource
     *
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            $fieldName = $this->getData('name');
            foreach ($dataSource['data']['items'] as &$item) {
                if (isset($item['entity_id'])) {
                    if (!empty($item['qty'])) {
                        $item['qty'] = $item['qty']*1;
                    }
                    $product = $this->productRepository->getById(
                        $item['entity_id']
                    );
                    
                    $item[$fieldName] = $product->getStatus();
                     
                }
            }
        }

        return $dataSource;
    }
}
