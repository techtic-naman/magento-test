<?php
namespace MageWorx\SocialProofBase\Model\ResourceModel\Campaign\Grid\Collection;

/**
 * Interceptor class for @see \MageWorx\SocialProofBase\Model\ResourceModel\Campaign\Grid\Collection
 */
class Interceptor extends \MageWorx\SocialProofBase\Model\ResourceModel\Campaign\Grid\Collection implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory, \Psr\Log\LoggerInterface $logger, \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy, \Magento\Framework\Event\ManagerInterface $eventManager, \Magento\Framework\Serialize\Serializer\Json $serializer, $mainTable, $eventPrefix, $eventObject, $resourceModel, ?\Magento\Framework\DB\Adapter\AdapterInterface $connection = null, ?\Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource = null, $model = 'Magento\\Framework\\View\\Element\\UiComponent\\DataProvider\\Document')
    {
        $this->___init();
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $serializer, $mainTable, $eventPrefix, $eventObject, $resourceModel, $connection, $resource, $model);
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
