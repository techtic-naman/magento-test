<?php
/**
 * Copyright © 2018 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\PersonalPromotion\Block\Adminhtml\Promo\Tab;

use Magento\Backend\Block\Widget\Grid;
use Magento\Customer\Model\ResourceModel\Customer\CollectionFactory;
use MageWorx\PersonalPromotion\Helper\Data as HelperData;
use MageWorx\PersonalPromotion\Helper\Rule as HelperRule;

class Customers extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * @var CollectionFactory
     */
    protected $customerFactory;

    /**
     * @var \MageWorx\PersonalPromotion\Model\ResourceModel\PersonalPromotion
     */
    protected $personalPromotionResourceModel;

    /**
     * @var HelperData
     */
    protected $helperData;

    /**
     * @var HelperRule
     */
    protected $helperRule;

    /**
     * Registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * Customers constructor.
     *
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param CollectionFactory $customerFactory
     * @param \MageWorx\PersonalPromotion\Model\ResourceModel\PersonalPromotion $personalPromotionResourceModel
     * @param HelperData $helperData
     * @param HelperRule $helperRule
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        CollectionFactory $customerFactory,
        \MageWorx\PersonalPromotion\Model\ResourceModel\PersonalPromotion $personalPromotionResourceModel,
        HelperData $helperData,
        HelperRule $helperRule,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
        parent::__construct($context, $backendHelper, $data);
        $this->customerFactory                = $customerFactory;
        $this->personalPromotionResourceModel = $personalPromotionResourceModel;
        $this->helperData                     = $helperData;
        $this->helperRule                     = $helperRule;
        $this->registry                       = $registry;
    }

    /**
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _construct()
    {
        parent::_construct();
        $nameSpace = $this->getRequest()->getParam('namespace');

        if ($nameSpace == 'salesrulestaging_update_form') {
            $this->setId('personalpromotion_promo_customers_staging');
        } else {
            $this->setId('personalpromotion_promo_customers');
        }

        $this->setDefaultSort('entity_id');
        $this->setDefaultFilter(['in_customer' => 1]);
        $this->setUseAjax(true);
    }

    /**
     * @param Grid\Column $column
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _addColumnFilterToCollection($column)
    {
        if ($column->getId() == 'in_customer') {
            $customerIds = $this->getSelectedCustomers();
            if (empty($customerIds)) {
                $customerIds = 0;
            }
            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('entity_id', ['in' => $customerIds]);
            } elseif (!empty($customerIds)) {
                $this->getCollection()->addFieldToFilter('entity_id', ['nin' => $customerIds]);
            }
        } else {
            parent::_addColumnFilterToCollection($column);
        }

        return $this;
    }

    /**
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareCollection()
    {
        $collection = $this->getCustomerCollection();
        $collection->joinField(
            'country_code',
            'customer_grid_flat',
            'billing_country_id',
            'entity_id=entity_id',
            null,
            'left'
        );

        $storeId = (int)$this->getRequest()->getParam('store', 0);
        if ($storeId > 0) {
            $collection->addStoreFilter($storeId);
        }
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * @return $this
     * @throws \Exception
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'in_customer',
            [
                'type'             => 'checkbox',
                'name'             => 'in_customer',
                'values'           => $this->getSelectedCustomers(),
                'index'            => 'entity_id',
                'header_css_class' => 'col-select col-massaction',
                'column_css_class' => 'col-select col-massaction'
            ]
        );

        $this->addColumn(
            'entity_id',
            [
                'header' => __('Customer ID'),
                'align'  => 'left',
                'index'  => 'entity_id',
                'width'  => '10px;'
            ]
        );

        $this->addColumn(
            'name',
            [
                'header' => __('Name'),
                'type'   => 'text',
                'align'  => 'right',
                'index'  => 'firstname',
                'width'  => '15px;',
            ]
        );


        $this->addColumn(
            'customer_email',
            [
                'header' => __('Customer Email'),
                'type'   => 'text',
                'align'  => 'center',
                'index'  => 'email',
                'width'  => '70px;',
            ]
        );

        $this->addColumn(
            'country_code',
            [
                'header'   => __('Country'),
                'type'     => 'text',
                'align'    => 'center',
                'index'    => 'country_code',
                'renderer' => '\MageWorx\PersonalPromotion\Block\Adminhtml\Promo\Tab\Customers\Renderer\Country',
                'width'    => '70px;',
            ]
        );

        $this->addColumn(
            'website_id',
            [
                'header'   => __('Website'),
                'type'     => 'text',
                'align'    => 'center',
                'index'    => 'website_id',
                'renderer' => '\MageWorx\PersonalPromotion\Block\Adminhtml\Promo\Tab\Customers\Renderer\Website',
                'width'    => '70px;',
            ]
        );

        return parent::_prepareColumns();
    }

    /**
     * @return \Magento\Customer\Model\ResourceModel\Customer\Collection
     */
    protected function getCustomerCollection()
    {
        return $this->customerFactory->create();
    }

    /**
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl(
            'mageworx_personalpromotion/promo_quote/grid',
            [
                '_current' => true,
                'row_id'   => $this->helperRule->getRuleId(
                    $this->getRequest()->getParams(),
                    $this->registry->registry(\Magento\SalesRule\Model\RegistryConstants::CURRENT_SALES_RULE)
                )
            ]
        );
    }

    /**
     * @return array
     * @throws \Zend_Db_Select_Exception
     */
    protected function getSelectedCustomers()
    {
        $priceRule = $this->registry->registry(\Magento\SalesRule\Model\RegistryConstants::CURRENT_SALES_RULE);
        $params    = $this->getRequest()->getParams();
        $ruleId    = $this->helperRule->getRuleId($params, $priceRule);

        if ($ruleId === null) {
            return [];
        }

        return $this->personalPromotionResourceModel->getCustomerIdsByRuleId($ruleId);
    }
}
