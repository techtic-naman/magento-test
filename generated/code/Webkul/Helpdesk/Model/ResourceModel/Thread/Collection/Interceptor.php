<?php
namespace Webkul\Helpdesk\Model\ResourceModel\Thread\Collection;

/**
 * Interceptor class for @see \Webkul\Helpdesk\Model\ResourceModel\Thread\Collection
 */
class Interceptor extends \Webkul\Helpdesk\Model\ResourceModel\Thread\Collection implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory, \Psr\Log\LoggerInterface $loggerInterface, \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategyInterface, \Magento\Framework\Event\ManagerInterface $eventManager, \Magento\Store\Model\StoreManagerInterface $storeManagerInterface, ?\Magento\Framework\DB\Adapter\AdapterInterface $connection = null, ?\Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource = null)
    {
        $this->___init();
        parent::__construct($entityFactory, $loggerInterface, $fetchStrategyInterface, $eventManager, $storeManagerInterface, $connection, $resource);
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
