<?php
namespace Magento\Framework\View\TemplateEngine\Php;

/**
 * Interceptor class for @see \Magento\Framework\View\TemplateEngine\Php
 */
class Interceptor extends \Magento\Framework\View\TemplateEngine\Php implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\ObjectManagerInterface $helperFactory, array $blockVariables = [])
    {
        $this->___init();
        parent::__construct($helperFactory, $blockVariables);
    }

    /**
     * {@inheritdoc}
     */
    public function render(\Magento\Framework\View\Element\BlockInterface $block, $fileName, array $dictionary = [])
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'render');
        return $pluginInfo ? $this->___callPlugins('render', func_get_args(), $pluginInfo) : parent::render($block, $fileName, $dictionary);
    }

    /**
     * {@inheritdoc}
     */
    public function __call($method, $args)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, '__call');
        return $pluginInfo ? $this->___callPlugins('__call', func_get_args(), $pluginInfo) : parent::__call($method, $args);
    }

    /**
     * {@inheritdoc}
     */
    public function __isset($name)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, '__isset');
        return $pluginInfo ? $this->___callPlugins('__isset', func_get_args(), $pluginInfo) : parent::__isset($name);
    }

    /**
     * {@inheritdoc}
     */
    public function __get($name)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, '__get');
        return $pluginInfo ? $this->___callPlugins('__get', func_get_args(), $pluginInfo) : parent::__get($name);
    }

    /**
     * {@inheritdoc}
     */
    public function helper($className)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'helper');
        return $pluginInfo ? $this->___callPlugins('helper', func_get_args(), $pluginInfo) : parent::helper($className);
    }
}
