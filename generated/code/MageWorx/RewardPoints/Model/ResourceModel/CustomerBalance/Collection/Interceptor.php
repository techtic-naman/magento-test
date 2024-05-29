<?php
namespace MageWorx\RewardPoints\Model\ResourceModel\CustomerBalance\Collection;

/**
 * Interceptor class for @see \MageWorx\RewardPoints\Model\ResourceModel\CustomerBalance\Collection
 */
class Interceptor extends \MageWorx\RewardPoints\Model\ResourceModel\CustomerBalance\Collection implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\Stdlib\DateTime\TimezoneInterface $date, \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory, \Psr\Log\LoggerInterface $logger, \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy, \Magento\Framework\Event\ManagerInterface $eventManager, \Magento\Customer\Model\Config\Share $shareConfig, \Magento\Framework\DB\Sql\ExpressionFactory $expressionFactory, ?\Magento\Framework\DB\Adapter\AdapterInterface $connection = null, ?\Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource = null)
    {
        $this->___init();
        parent::__construct($date, $entityFactory, $logger, $fetchStrategy, $eventManager, $shareConfig, $expressionFactory, $connection, $resource);
    }

    /**
     * {@inheritdoc}
     */
    public function getCurPage($displacement = 0)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getCurPage');
        return $pluginInfo ? $this->___callPlugins('getCurPage', func_get_args(), $pluginInfo) : parent::getCurPage($displacement);
    }
}
