<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\RewardPoints\Block\Customer\Account;

use \MageWorx\RewardPoints\Model\PointTransaction;


class Transactions extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \MageWorx\RewardPoints\Helper\Data
     */
    protected $helperData;

    /**
     * @var \Magento\Customer\Helper\Session\CurrentCustomer
     */
    protected $currentCustomer;

    /**
     * @var \MageWorx\RewardPoints\Model\ResourceModel\PointTransaction\Collection
     */
    protected $pointTransactionCollection;

    /**
     * @var \MageWorx\RewardPoints\Model\ResourceModel\PointTransaction\CollectionFactory
     */
    protected $pointTransactionCollectionFactory;

    /**
     * @var \MageWorx\RewardPoints\Model\PointTransactionMessageMaker
     */
    protected $pointTransactionMessageMaker;

    /**
     * Transactions constructor.
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \MageWorx\RewardPoints\Helper\Data $helperData
     * @param \Magento\Customer\Helper\Session\CurrentCustomer $currentCustomer
     * @param \MageWorx\RewardPoints\Model\ResourceModel\PointTransaction\CollectionFactory $transactionCollectionFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \MageWorx\RewardPoints\Helper\Data $helperData,
        \Magento\Customer\Helper\Session\CurrentCustomer $currentCustomer,
        \MageWorx\RewardPoints\Model\ResourceModel\PointTransaction\CollectionFactory $transactionCollectionFactory,
        \MageWorx\RewardPoints\Model\PointTransactionMessageMaker $pointTransactionMessageMaker,
        array $data = []
    ) {
        $this->helperData                        = $helperData;
        $this->currentCustomer                   = $currentCustomer;
        $this->pointTransactionCollectionFactory = $transactionCollectionFactory;
        $this->pointTransactionMessageMaker      = $pointTransactionMessageMaker;

        parent::__construct($context, $data);
    }

    /**
     * @param PointTransaction $item
     * @return string
     */
    public function getPointsDelta(PointTransaction $item)
    {
        $prefix = ($item->getPointsDelta() > 0) ? '+' : '';

        return $prefix . $item->getPointsDelta();
    }

    /**
     * @return \MageWorx\RewardPoints\Api\Data\PointTransactionInterface[]
     */
    public function getTransactions()
    {
        return $this->getCollection()->getItems();
    }

    /**
     * @param PointTransaction $item
     * @return string
     */
    public function getPointsBalance(PointTransaction $item)
    {
        return $item->getPointsBalance();
    }

    /**
     * @param PointTransaction $item
     * @return string
     */
    public function getMessage(PointTransaction $item)
    {
        return $this->pointTransactionMessageMaker->getTransactionMessage(
            $item->getEventCode(),
            $item->getEventData(),
            $item->getComment()
        );
    }

    /**
     * @param PointTransaction $item
     * @return string
     */
    public function getDate(PointTransaction $item)
    {
        return $this->formatDate($item->getCreatedAt(), \IntlDateFormatter::SHORT, true);
    }

    /**
     * @return \MageWorx\RewardPoints\Model\ResourceModel\PointTransaction\Collection
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getCollection()
    {
        if (null === $this->pointTransactionCollection) {
            $websiteId = $this->_storeManager->getWebsite()->getId();

            $this->pointTransactionCollection = $this->pointTransactionCollectionFactory->create();
            $this->pointTransactionCollection->addCustomerFilter($this->currentCustomer->getCustomerId())
                                             ->addWebsiteFilter($websiteId)
                                             ->setDefaultOrder();
        }

        return $this->pointTransactionCollection;
    }

    /**
     * @return $this
     */
    protected function _prepareLayout()
    {
        if ($this->helperData->isEnableForCustomer()) {
            /** @var \Magento\Theme\Block\Html\Pager $pagerBlock */
            $pagerBlock = $this->getLayout()->createBlock(
                \Magento\Theme\Block\Html\Pager::class,
                'rewardpoints.transactions.pager'
            )->setCollection(
                $this->getCollection()
            );
            $this->setChild('pager', $pagerBlock);
        }

        return parent::_prepareLayout();
    }

    /**
     * @return string
     */
    protected function _toHtml()
    {
        if (!$this->helperData->isEnableForCustomer()) {
            return '';
        }

        return parent::_toHtml();
    }
}
