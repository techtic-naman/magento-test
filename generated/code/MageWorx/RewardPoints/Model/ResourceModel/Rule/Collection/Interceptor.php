<?php
namespace MageWorx\RewardPoints\Model\ResourceModel\Rule\Collection;

/**
 * Interceptor class for @see \MageWorx\RewardPoints\Model\ResourceModel\Rule\Collection
 */
class Interceptor extends \MageWorx\RewardPoints\Model\ResourceModel\Rule\Collection implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\Data\Collection\EntityFactory $entityFactory, \Psr\Log\LoggerInterface $logger, \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy, \Magento\Framework\Event\ManagerInterface $eventManager, \Magento\Framework\Stdlib\DateTime\TimezoneInterface $date, \Magento\SalesRule\Model\ResourceModel\Rule\DateApplier $dateApplier, \Magento\Framework\Serialize\Serializer\Json $serializer, \MageWorx\RewardPoints\Model\ResourceModel\Rule\AssociatedEntityMap $associatedEntityMap, ?\Magento\Framework\DB\Adapter\AdapterInterface $connection = null, ?\Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource = null)
    {
        $this->___init();
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $date, $dateApplier, $serializer, $associatedEntityMap, $connection, $resource);
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
