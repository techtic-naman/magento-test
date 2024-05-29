<?php
namespace PayPal\Braintree\Ui\Component\Report\DataProvider\FilterPool;

/**
 * Interceptor class for @see \PayPal\Braintree\Ui\Component\Report\DataProvider\FilterPool
 */
class Interceptor extends \PayPal\Braintree\Ui\Component\Report\DataProvider\FilterPool implements \Magento\Framework\Interception\InterceptorInterface
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
    public function applyFilters(\Magento\Framework\Data\Collection $collection, \Magento\Framework\Api\Search\SearchCriteriaInterface $criteria) : void
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'applyFilters');
        $pluginInfo ? $this->___callPlugins('applyFilters', func_get_args(), $pluginInfo) : parent::applyFilters($collection, $criteria);
    }
}
