<?php
/**
 *
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\RewardPoints\Controller\Adminhtml\Reward\Rule;

class Index extends \MageWorx\RewardPoints\Controller\Adminhtml\Reward\Rule
{
    /**
     * Index action
     *
     * @return void
     */
    public function execute()
    {
        $this->_initAction()->_addBreadcrumb(__('Marketing'), __('Reward Points Rules'));
        $this->_view->getPage()->getConfig()->getTitle()->prepend(__('Reward Points Rules'));
        $this->_view->renderLayout();
    }
}
