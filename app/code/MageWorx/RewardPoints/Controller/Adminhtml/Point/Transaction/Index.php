<?php
/**
 *
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\RewardPoints\Controller\Adminhtml\Point\Transaction;

class Index extends \MageWorx\RewardPoints\Controller\Adminhtml\Point\Transaction
{
    /**
     * Index action
     *
     * @return void
     */
    public function execute()
    {
        $this->_initAction()->_addBreadcrumb(__('Marketing'), __('Points Transactions'));
        $this->_view->getPage()->getConfig()->getTitle()->prepend(__('Points Transactions'));
        $this->_view->renderLayout();
    }
}
