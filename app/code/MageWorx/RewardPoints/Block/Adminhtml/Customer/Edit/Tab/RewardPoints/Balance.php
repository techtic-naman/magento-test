<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\RewardPoints\Block\Adminhtml\Customer\Edit\Tab\RewardPoints;

class Balance extends \Magento\Backend\Block\Template
{
    /**
     * @var string
     */
    protected $_template = 'customer/edit/rewardpoints/balance.phtml';

    /**
     * @return $this
     */
    protected function _prepareLayout()
    {
        $grid = $this->getLayout()->createBlock(
            \MageWorx\RewardPoints\Block\Adminhtml\Customer\Edit\Tab\RewardPoints\Balance\Grid::class
        );
        $this->setChild('grid', $grid);

        return parent::_prepareLayout();
    }
}
