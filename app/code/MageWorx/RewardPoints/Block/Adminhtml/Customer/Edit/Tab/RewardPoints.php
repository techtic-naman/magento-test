<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\RewardPoints\Block\Adminhtml\Customer\Edit\Tab;

use Magento\Backend\Block\Widget;
use Magento\Customer\Controller\RegistryConstants;
use Magento\Ui\Component\Layout\Tabs\TabInterface;

class RewardPoints extends Widget implements TabInterface
{
    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
        $this->registry = $registry;
        parent::__construct($context, $data);
    }

    /**
     * Set identifier and title
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
    }

    /**
     * {@inheritdoc}
     */
    public function getTabLabel()
    {
        return __('Reward Points');
    }

    /**
     * {@inheritdoc}
     */
    public function getTabTitle()
    {
        return $this->getTabLabel();
    }

    /**
     * {@inheritdoc}
     */
    public function canShowTab()
    {
        $customerId = $this->registry->registry(RegistryConstants::CURRENT_CUSTOMER_ID);

        return (bool)$customerId;
    }

    /**
     * {@inheritdoc}
     */
    public function isHidden()
    {
        if (!$this->getRequest()->getParam('id')) {
            return true;
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function getTabClass()
    {
        return '';
    }

    /**
     * {@inheritdoc}
     */
    public function getAfter()
    {
        return 'orders';
    }

    /**
     * {@inheritdoc}
     */
    public function getTabUrl()
    {
        return '';
    }

    /**
     * {@inheritdoc}
     */
    public function isAjaxLoaded()
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    protected function _prepareLayout()
    {
        /** @var \MageWorx\RewardPoints\Block\Adminhtml\Customer\Edit\Tab\RewardPoints\Balance $displayBalanceBlock */
        $displayBalanceBlock = $this->getLayout()->createBlock(
            \MageWorx\RewardPoints\Block\Adminhtml\Customer\Edit\Tab\RewardPoints\Balance::class
        );
        $this->setChild('display_balance', $displayBalanceBlock);

        /** @var \MageWorx\RewardPoints\Block\Adminhtml\Customer\Edit\Tab\RewardPoints\Form $updateBalanceBlock */
        $updateBalanceBlock = $this->getLayout()->createBlock(
            \MageWorx\RewardPoints\Block\Adminhtml\Customer\Edit\Tab\RewardPoints\Form::class
        );
        $this->setChild('update_balance', $updateBalanceBlock);

        /** @var \Magento\Backend\Block\Widget\Accordion $gridWrapper */
        $gridWrapper = $this->getLayout()->createBlock(\Magento\Backend\Block\Widget\Accordion::class);
        $ajaxUrl     = $this->getUrl('mageworx_rewardpoints/customer_rewardpoints/transactions', ['_current' => true]);

        $gridWrapper->addItem(
            'mageworx_rewardpoints_customer_transactions',
            [
                'title'       => __('Reward Points Transactions'),
                'content_url' => $ajaxUrl,
                'ajax'        => true,
                'open'        => false,
                'class'       => '',
            ]
        );
        $this->setChild('grid_wrapper', $gridWrapper);

        return parent::_prepareLayout();
    }
}
