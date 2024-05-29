<?php
/**
 *
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\RewardPoints\Controller\Adminhtml\Reward\Rule;

class NewAction extends \MageWorx\RewardPoints\Controller\Adminhtml\Reward\Rule
{
    /**
     * New reward rule action
     *
     * @return void
     */
    public function execute()
    {
        $this->_forward('edit');
    }
}
