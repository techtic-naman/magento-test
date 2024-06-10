<?php
namespace Magento\Elasticsearch7\SearchAdapter\Adapter;

/**
 * Interceptor class for @see \Magento\Elasticsearch7\SearchAdapter\Adapter
 */
class Interceptor extends \Magento\Elasticsearch7\SearchAdapter\Adapter implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Elasticsearch\SearchAdapter\ConnectionManager $connectionManager, \Magento\Elasticsearch7\SearchAdapter\Mapper $mapper, \Magento\Elasticsearch\SearchAdapter\ResponseFactory $responseFactory, \Magento\Elasticsearch\SearchAdapter\Aggregation\Builder $aggregationBuilder, \Magento\Elasticsearch\SearchAdapter\QueryContainerFactory $queryContainerFactory, \Psr\Log\LoggerInterface $logger)
    {
        $this->___init();
        parent::__construct($connectionManager, $mapper, $responseFactory, $aggregationBuilder, $queryContainerFactory, $logger);
    }

    /**
     * {@inheritdoc}
     */
    public function query(\Magento\Framework\Search\RequestInterface $request) : \Magento\Framework\Search\Response\QueryResponse
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'query');
        return $pluginInfo ? $this->___callPlugins('query', func_get_args(), $pluginInfo) : parent::query($request);
    }
}
