<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\RewardPoints\Controller\Adminhtml\Dashboard;

class Statistics extends \Magento\Backend\Controller\Adminhtml\Dashboard\AjaxBlock
{
    /**
     * Gets reward statistics
     *
     * @return \Magento\Framework\Controller\Result\Raw
     */
    public function execute()
    {
        $output = $this->layoutFactory->create()
                                      ->createBlock(\MageWorx\RewardPoints\Block\Adminhtml\Dashboard\Tab\Statistics::class)
                                      ->toHtml();
        $resultRaw = $this->resultRawFactory->create();

        return $resultRaw->setContents($output);
    }
}
