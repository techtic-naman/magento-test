<?php
namespace MageWorx\GeoIP\Model\ResourceModel\RegionRelations\Grid\Collection;

/**
 * Interceptor class for @see \MageWorx\GeoIP\Model\ResourceModel\RegionRelations\Grid\Collection
 */
class Interceptor extends \MageWorx\GeoIP\Model\ResourceModel\RegionRelations\Grid\Collection implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Store\Api\StoreRepositoryInterface $storeRepository, \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory, \Psr\Log\LoggerInterface $logger, \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy, \Magento\Framework\Event\ManagerInterface $eventManager, $eventPrefix, $eventObject, $resourceModel, $mainTable = 'directory_country_region', $model = 'Magento\\Framework\\View\\Element\\UiComponent\\DataProvider\\Document', ?\Magento\Framework\DB\Adapter\AdapterInterface $connection = null, ?\Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource = null)
    {
        $this->___init();
        parent::__construct($storeRepository, $entityFactory, $logger, $fetchStrategy, $eventManager, $eventPrefix, $eventObject, $resourceModel, $mainTable, $model, $connection, $resource);
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
