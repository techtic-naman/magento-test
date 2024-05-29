<?php
namespace Magento\Framework\Mview\View\Subscription;

/**
 * Interceptor class for @see \Magento\Framework\Mview\View\Subscription
 */
class Interceptor extends \Magento\Framework\Mview\View\Subscription implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\App\ResourceConnection $resource, \Magento\Framework\DB\Ddl\TriggerFactory $triggerFactory, \Magento\Framework\Mview\View\CollectionInterface $viewCollection, \Magento\Framework\Mview\ViewInterface $view, $tableName, $columnName, $ignoredUpdateColumns = [], $ignoredUpdateColumnsBySubscription = [], ?\Magento\Framework\Mview\Config $mviewConfig = null)
    {
        $this->___init();
        parent::__construct($resource, $triggerFactory, $viewCollection, $view, $tableName, $columnName, $ignoredUpdateColumns, $ignoredUpdateColumnsBySubscription, $mviewConfig);
    }

    /**
     * {@inheritdoc}
     */
    public function create(bool $save = true)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'create');
        return $pluginInfo ? $this->___callPlugins('create', func_get_args(), $pluginInfo) : parent::create($save);
    }

    /**
     * {@inheritdoc}
     */
    public function remove()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'remove');
        return $pluginInfo ? $this->___callPlugins('remove', func_get_args(), $pluginInfo) : parent::remove();
    }
}
