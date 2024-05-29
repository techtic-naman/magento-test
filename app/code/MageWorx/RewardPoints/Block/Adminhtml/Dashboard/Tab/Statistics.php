<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\RewardPoints\Block\Adminhtml\Dashboard\Tab;

class Statistics extends \Magento\Backend\Block\Dashboard\Grid
{
    /**
     * @var \MageWorx\RewardPoints\Model\ResourceModel\CustomerBalance\Report\CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var \MageWorx\RewardPoints\Model\PointCurrencyConverter
     */
    protected $pointCurrencyConverter;

    /**
     * @var string
     */
    protected $_template = 'Magento_Backend::dashboard/grid.phtml';

    /**
     * Statistics constructor.
     *
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \MageWorx\RewardPoints\Model\PointCurrencyConverter $pointCurrencyConverter
     * @param \MageWorx\RewardPoints\Model\ResourceModel\CustomerBalance\Report\CollectionFactory $collectionFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \MageWorx\RewardPoints\Model\PointCurrencyConverter $pointCurrencyConverter,
        \MageWorx\RewardPoints\Model\ResourceModel\CustomerBalance\Report\CollectionFactory $collectionFactory,
        array $data = []
    ) {
        $this->pointCurrencyConverter = $pointCurrencyConverter;
        $this->collectionFactory      = $collectionFactory;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('rewardPointsGrid');
    }

    /**
     * {@inheritdoc}
     */
    protected function _prepareCollection()
    {
        /** @var \MageWorx\RewardPoints\Model\ResourceModel\CustomerBalance\Report\Collection $collection */
        $collection = $this->collectionFactory->create();

        if ($this->getParam('store')) {
            $websiteId = $this->_storeManager->getStore($this->getParam('store'))->getWebsiteId();
        } elseif ($this->getParam('website')) {
            $websiteId = $this->_storeManager->getWebsite($this->getParam('website'))->getId();
        } elseif ($this->getParam('group')) {
            $websiteId = $this->_storeManager->getGroup($this->getParam('group'))->getWebsiteId();
        }

        $collection->addWebsiteStatistics();

        if (isset($websiteId)) {
            $collection->addWebsiteFilter('website', ['in' => $websiteId]);
        }

        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * @return $this
     */
    protected function _afterLoadCollection()
    {
        foreach ($this->getCollection() as $item) {

            $currencyAmount = $this->pointCurrencyConverter->getCurrencyByPoints(
                $item->getAmount(),
                $item->getWebsiteId(),
                'website'
            );

            $item->setCurrencyAmount($currencyAmount);
        }

        return parent::_afterLoadCollection();
    }

    /**
     * {@inheritdoc}
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'name',
            [
                'header'   => __('Website'),
                'sortable' => false,
                'index'    => 'website_name'
            ]
        );

        $this->addColumn(
            'count',
            [
                'header'           => __('Customers\' Balances'),
                'sortable'         => false,
                'index'            => 'count',
                'type'             => 'number',
                'header_css_class' => 'col-orders',
                'column_css_class' => 'col-orders'
            ]
        );

        $baseCurrencyCode = (string)$this->_storeManager->getStore(
            (int)$this->getParam('store')
        )->getBaseCurrencyCode();

        $this->addColumn(
            'points',
            [
                'header'           => __('Points Amount'),
                'sortable'         => false,
                'type'             => 'number',
                'index'            => 'amount',
                'header_css_class' => 'col-total',
                'column_css_class' => 'col-total'
            ]
        );

        $this->addColumn(
            'currency_amount',
            [
                'header'           => __('Total'),
                'sortable'         => false,
                'type'             => 'currency',
                'currency_code'    => $baseCurrencyCode,
                'index'            => 'currency_amount',
                'renderer'         => \Magento\Reports\Block\Adminhtml\Grid\Column\Renderer\Currency::class,
                'header_css_class' => 'col-total',
                'column_css_class' => 'col-total'
            ]
        );

        $this->setFilterVisibility(false);
        $this->setPagerVisibility(false);

        return parent::_prepareColumns();
    }

    /**
     * {@inheritdoc}
     */
    public function getRowUrl($row)
    {
        return null;
    }
}
