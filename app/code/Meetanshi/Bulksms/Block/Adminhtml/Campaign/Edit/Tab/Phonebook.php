<?php

namespace Meetanshi\Bulksms\Block\Adminhtml\Campaign\Edit\Tab;

use Magento\Backend\Block\Template\Context;
use Magento\Backend\Helper\Data;
use Magento\Framework\Registry;
use Meetanshi\Bulksms\Model\BulksmsFactory;
use Meetanshi\Bulksms\Model\CampaignFactory;

class Phonebook extends \Magento\Backend\Block\Widget\Grid\Extended
{
    protected $coreRegistry = null;
    protected $bulksmsFactory;
    protected $capmaignFactory;

    public function __construct(
        Context $context,
        Data $backendHelper,
        BulksmsFactory $bulksmsFactory,
        CampaignFactory $capmaignFactory,
        Registry $coreRegistry,
        array $data = []
    ) {
        $this->bulksmsFactory = $bulksmsFactory;
        $this->capmaignFactory = $capmaignFactory;
        $this->coreRegistry = $coreRegistry;
        parent::__construct($context, $backendHelper, $data);
    }

    protected function _construct()
    {
        parent::_construct();
        $this->setId('phonebook_grid');
        $this->setDefaultFilter(['phonebook_id' => 1]);
        $this->setDefaultSort('id');
        $this->setUseAjax(true);
        $this->setSaveParametersInSession(true);
    }

    protected function _addColumnFilterToCollection($column)
    {
        if ($column->getId() == 'phonebook_id') {
            $productIds = $this->_getSelectedProducts();
            if (empty($productIds)) {
                $productIds = 0;
            }
            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('id', ['in' => $productIds]);
            } else {
                if ($productIds) {
                    $this->getCollection()->addFieldToFilter('id', ['nin' => $productIds]);
                }
            }
        } else {
            parent::_addColumnFilterToCollection($column);
        }
        return $this;
    }

    protected function _prepareCollection()
    {
        $collection = $this->bulksmsFactory->create()->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn(
            'phonebook_id',
            [
                'type' => 'checkbox',
                'field_name' => 'phonebook_id',
                'required' => true,
                'values' => $this->_getSelectedProducts(),
                'align' => 'center',
                'index' => 'id',
                'header_css_class' => 'col-select',
                'column_css_class' => 'col-select'
            ]
        );

        $this->addColumn(
            'phonebook_name',
            [
                'header' => __('Name'),
                'index' => 'name',
                'header_css_class' => 'col-name',
                'column_css_class' => 'col-name'
            ]
        );

        $this->addColumn(
            'mobilenumber',
            [
                'header' => __('Mobile Number'),
                'index' => 'mobilenumber',
                'header_css_class' => 'col-sku',
                'column_css_class' => 'col-sku'
            ]
        );

        return parent::_prepareColumns();
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/*/contacts', ['_current' => true]);
    }

    protected function _getSelectedProducts()
    {
        return $this->getSelectedProducts();
    }

    public function getSelectedProducts()
    {
        $proIds= [];
        if ($products=$this->getRequest()->getParam('contact_products')) {
            foreach ($products as $product) {
                $proIds[$product] = ['id' =>$product];
            }
            return array_keys($proIds);
        }

        $row_id= $this->getRequest()->getParam('id');
        if (!isset($row_id)) {
            $row_id= 0;
        }

        $collection = $this->capmaignFactory->create()->load($row_id);
        $data = $collection->getPhonebookId();
        $products = explode(',', $data);

        $proIds = [];

        foreach ($products as $product) {
            $proIds[$product] = ['id' => $product];
        }
        return array_keys($proIds);
    }
    /**
     * @return bool
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * @return bool
     */
    public function isHidden()
    {
        return true;
    }
}
