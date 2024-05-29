<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\RewardPoints\Model\ResourceModel\CustomerBalance;

use Magento\Customer\Model\Config\Share;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $date;

    /**
     * @var Share
     */
    private $shareConfig;

    /**
     * @var \Magento\Framework\DB\Sql\ExpressionFactory
     */
    protected $expressionFactory;

    /**
     * Collection constructor.
     *
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $date
     * @param \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param Share $shareConfig
     * @param \Magento\Framework\DB\Sql\ExpressionFactory $expressionFactory
     * @param \Magento\Framework\DB\Adapter\AdapterInterface|null $connection
     * @param \Magento\Framework\Model\ResourceModel\Db\AbstractDb|null $resource
     */
    public function __construct(
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $date,
        \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        Share $shareConfig,
        \Magento\Framework\DB\Sql\ExpressionFactory $expressionFactory,
        \Magento\Framework\DB\Adapter\AdapterInterface $connection = null,
        \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource = null
    ) {
        $this->date              = $date;
        $this->shareConfig       = $shareConfig;
        $this->expressionFactory = $expressionFactory;
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $connection, $resource);
    }

    /**
     * Internal constructor
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            \MageWorx\RewardPoints\Model\CustomerBalance::class,
            \MageWorx\RewardPoints\Model\ResourceModel\CustomerBalance::class
        );
    }

    /**
     * Add filter by website id
     *
     * @param int $websiteId
     * @return $this
     */
    public function addWebsiteFilter($websiteId)
    {
        $this->getSelect()->where('main_table.website_id = ?', $websiteId);

        return $this;
    }

    /**
     * @param null|string $date Format: Y-m-d
     * @return $this
     */
    public function addExpiredDateFilter($date = null)
    {
        $date = ($date === null) ? $this->date->date()->format('Y-m-d') : $date;

        $this->getSelect()->where(
            'expiration_date < ?',
            $date
        );

        return $this;
    }

    /**
     * @return $this
     */
    public function addPositivePointBalanceFilter()
    {
        return $this->addFieldToFilter('points', ['gt' => 0]);
    }

    /**
     * @return $this
     */
    public function addNotNullExpirationDateFilter()
    {
        return $this->addFieldToFilter('expiration_date', ['notnull' => true]);
    }

    /**
     * @return $this
     */
    public function joinWebsiteTable()
    {
        $this->getSelect()->join(
            ['website_table' => $this->getTable('store_website')],
            'main_table.website_id = website_table.website_id',
            ['website_code' => 'website_table.code']
        );

        return $this;
    }

    /**
     * @return $this
     */
    public function joinCustomerTable()
    {
        $condition = 'main_table.customer_id = customer_table.entity_id';
        if (!$this->shareConfig->isGlobalScope()) {
            $condition = $condition . ' AND main_table.website_id = customer_table.website_id';
        }

        $this->getSelect()->join(
            ['customer_table' => $this->getTable('customer_entity')],
            $condition,
            ['customer_email' => 'customer_table.email']
        );

        return $this;
    }
}
