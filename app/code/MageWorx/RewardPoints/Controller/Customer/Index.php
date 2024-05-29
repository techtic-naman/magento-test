<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\RewardPoints\Controller\Customer;


class Index extends \MageWorx\RewardPoints\Controller\Customer
{
    /**
     * @return void
     */
    public function execute()
    {
        if (!$this->helperData->isEnableForCustomer()) {
            $this->_redirect('noroute');

            return;
        }

        $this->_view->loadLayout();
        $this->_view->getPage()->getConfig()->getTitle()->set(__('Reward Points'));
        $this->_view->renderLayout();
    }
}
