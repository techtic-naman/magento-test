<?php
namespace Magento\Framework\View\Element\UiComponent\DataProvider\FilterPool;

/**
 * Interceptor class for @see \Magento\Framework\View\Element\UiComponent\DataProvider\FilterPool
 */
class Interceptor extends \Magento\Framework\View\Element\UiComponent\DataProvider\FilterPool implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(array $appliers = [])
    {
        $this->___init();
        parent::__construct($appliers);
    }

    /**
     * {@inheritdoc}
     */
    public function applyFilters(\Magento\Framework\Data\Collection $collection, \Magento\Framework\Api\Search\SearchCriteriaInterface $criteria)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'applyFilters');
        return $pluginInfo ? $this->___callPlugins('applyFilters', func_get_args(), $pluginInfo) : parent::applyFilters($collection, $criteria);
    }
}
