<?php
namespace Webkul\Helpdesk\Model\ResourceModel\Tickets\Grid\Collection;

/**
 * Interceptor class for @see \Webkul\Helpdesk\Model\ResourceModel\Tickets\Grid\Collection
 */
class Interceptor extends \Webkul\Helpdesk\Model\ResourceModel\Tickets\Grid\Collection implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactoryInterface, \Psr\Log\LoggerInterface $loggerInterface, \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategyInterface, \Magento\Framework\Event\ManagerInterface $eventManagerInterface, \Magento\Store\Model\StoreManagerInterface $storeManagerInterface, \Magento\Backend\Model\Auth\Session $session, \Webkul\Helpdesk\Helper\Data $data, $mainTable, $eventPrefix, $eventObject, $resourceModel, $model = 'Magento\\Framework\\View\\Element\\UiComponent\\DataProvider\\Document', $connection = null, ?\Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource = null)
    {
        $this->___init();
        parent::__construct($entityFactoryInterface, $loggerInterface, $fetchStrategyInterface, $eventManagerInterface, $storeManagerInterface, $session, $data, $mainTable, $eventPrefix, $eventObject, $resourceModel, $model, $connection, $resource);
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
