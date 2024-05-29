<?php
namespace Magento\Framework\Code\NameBuilder;

/**
 * Interceptor class for @see \Magento\Framework\Code\NameBuilder
 */
class Interceptor extends \Magento\Framework\Code\NameBuilder implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct()
    {
        $this->___init();
    }

    /**
     * {@inheritdoc}
     */
    public function buildClassName($parts)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'buildClassName');
        return $pluginInfo ? $this->___callPlugins('buildClassName', func_get_args(), $pluginInfo) : parent::buildClassName($parts);
    }
}
