<?php
namespace Magento\Framework\EntityManager\TypeResolver;

/**
 * Interceptor class for @see \Magento\Framework\EntityManager\TypeResolver
 */
class Interceptor extends \Magento\Framework\EntityManager\TypeResolver implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\EntityManager\MetadataPool $metadataPool)
    {
        $this->___init();
        parent::__construct($metadataPool);
    }

    /**
     * {@inheritdoc}
     */
    public function resolve($type)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'resolve');
        return $pluginInfo ? $this->___callPlugins('resolve', func_get_args(), $pluginInfo) : parent::resolve($type);
    }
}
