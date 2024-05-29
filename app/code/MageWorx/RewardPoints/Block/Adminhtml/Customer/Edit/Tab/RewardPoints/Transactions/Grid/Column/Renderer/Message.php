<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\RewardPoints\Block\Adminhtml\Customer\Edit\Tab\RewardPoints\Transactions\Grid\Column\Renderer;

class Message extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    /**
     * @var \MageWorx\RewardPoints\Model\PointTransactionMessageMaker
     */
    protected $pointTransactionMessageMaker;


    public function __construct(
        \MageWorx\RewardPoints\Model\PointTransactionMessageMaker $pointTransactionMessageMaker,
        \Magento\Backend\Block\Context $context,
        array $data = []
    ) {
        $this->pointTransactionMessageMaker = $pointTransactionMessageMaker;
        parent::__construct($context, $data);
    }

    /**
     * @param \Magento\Framework\DataObject $row
     * @return mixed|string
     */
    protected function _getValue(\Magento\Framework\DataObject $row)
    {
        return $this->pointTransactionMessageMaker->getTransactionMessage(
            $row->getEventCode(),
            $row->getEventData(),
            $row->getComment()
        );
    }
}
