<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\RewardPoints\Block\Adminhtml\Customer\Edit\Tab\RewardPoints\Balance;

use Magento\Customer\Controller\RegistryConstants;
use MageWorx\RewardPoints\Block\Adminhtml\Customer\Edit\Tab\RewardPoints\Balance\Grid\Column\Renderer\ExpirationDate;

class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * @var \MageWorx\RewardPoints\Helper\Data
     */
    protected $helperData;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \MageWorx\RewardPoints\Model\Source\WebsiteFactory
     */
    protected $websiteFactory;

    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    protected $customerRepository;

    /**
     * @var \MageWorx\RewardPoints\Model\ResourceModel\CustomerBalance\CollectionFactory
     */
    protected $customerBalanceCollectionFactory;

    /**
     * Grid constructor.
     *
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \MageWorx\RewardPoints\Helper\Data $helperData
     * @param \Magento\Framework\Registry $registry
     * @param \MageWorx\RewardPoints\Model\Source\WebsiteFactory $websiteFactory
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
     * @param \MageWorx\RewardPoints\Model\ResourceModel\CustomerBalance\CollectionFactory $customerBalanceCollectionFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \MageWorx\RewardPoints\Helper\Data $helperData,
        \Magento\Framework\Registry $registry,
        \MageWorx\RewardPoints\Model\Source\WebsiteFactory $websiteFactory,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \MageWorx\RewardPoints\Model\ResourceModel\CustomerBalance\CollectionFactory $customerBalanceCollectionFactory,
        array $data = []
    ) {
        $this->registry                         = $registry;
        $this->helperData                       = $helperData;
        $this->customerBalanceCollectionFactory = $customerBalanceCollectionFactory;
        $this->websiteFactory                   = $websiteFactory;
        $this->customerRepository               = $customerRepository;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('rewardPointsBalanceGrid');
        $this->setUseAjax(true);
        $this->setFilterVisibility(false);
        $this->setPagerVisibility(false);
    }

    /**
     * @return \Magento\Customer\Model\Customer
     */
    public function getCustomer()
    {
        $customerId = $this->registry->registry(RegistryConstants::CURRENT_CUSTOMER_ID);

        return $this->customerRepository->getById($customerId);
    }

    /**
     * {@inheritdoc}
     */
    protected function _prepareCollection()
    {
        $collection = $this->customerBalanceCollectionFactory->create()->addFieldToFilter(
            'customer_id',
            $this->getCustomer()->getId()
        );
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * {@inheritdoc}
     */
    protected function _afterLoadCollection()
    {
        parent::_afterLoadCollection();
        /* @var $item \MageWorx\RewardPoints\Api\Data\CustomerBalanceInterface */
        foreach ($this->getCollection() as $item) {
            $item->setCustomer($this->getCustomer());
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    protected function _prepareColumns()
    {
        if (!$this->_storeManager->isSingleStoreMode()) {
            $this->addColumn(
                'website_id',
                [
                    'header'   => __('Website'),
                    'index'    => 'website_id',
                    'type'     => 'options',
                    'options'  => $this->websiteFactory->create()->toOptionArray(false),
                    'sortable' => false
                ]
            );
        }

        $this->addColumn(
            'points',
            ['header' => __('Balance'), 'index' => 'points', 'sortable' => false, 'align' => 'center']
        );

        $this->addColumn(
            'currency_amount',
            [
                'header'   => __('Currency Amount'),
                'getter'   => 'getFormatedCurrencyAmount',
                'align'    => 'right',
                'sortable' => false
            ]
        );

        $this->addColumn(
            'expiration_date',
            [
                'header'   => __('Expiration Date'),
                'renderer' => ExpirationDate::class,
                'align'    => 'right',
                'sortable' => false
            ]
        );

        return parent::_prepareColumns();
    }

    /**
     * {@inheritdoc}
     */
    public function getRowUrl($row)
    {
        return '';
    }
}
