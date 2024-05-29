<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\RewardPoints\Block\Adminhtml\Customer\Edit\Tab\RewardPoints\Transactions;

class Grid extends \Magento\Backend\Block\Widget\Grid
{
    /**
     * {@inheritdoc}
     */
    protected function _prepareCollection()
    {
        $customerId = $this->getRequest()->getParam('id', 0);
        $this->getCollection()->addCustomerFilter($customerId);

        return parent::_prepareCollection();
    }
}
