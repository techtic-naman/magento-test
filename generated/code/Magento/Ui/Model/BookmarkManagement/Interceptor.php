<?php
namespace Magento\Ui\Model\BookmarkManagement;

/**
 * Interceptor class for @see \Magento\Ui\Model\BookmarkManagement
 */
class Interceptor extends \Magento\Ui\Model\BookmarkManagement implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Ui\Api\BookmarkRepositoryInterface $bookmarkRepository, \Magento\Framework\Api\FilterBuilder $filterBuilder, \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder, \Magento\Authorization\Model\UserContextInterface $userContext)
    {
        $this->___init();
        parent::__construct($bookmarkRepository, $filterBuilder, $searchCriteriaBuilder, $userContext);
    }

    /**
     * {@inheritdoc}
     */
    public function loadByNamespace($namespace)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'loadByNamespace');
        return $pluginInfo ? $this->___callPlugins('loadByNamespace', func_get_args(), $pluginInfo) : parent::loadByNamespace($namespace);
    }

    /**
     * {@inheritdoc}
     */
    public function getByIdentifierNamespace($identifier, $namespace)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getByIdentifierNamespace');
        return $pluginInfo ? $this->___callPlugins('getByIdentifierNamespace', func_get_args(), $pluginInfo) : parent::getByIdentifierNamespace($identifier, $namespace);
    }
}
