<?php
namespace Magento\Backend\Block\System\Store\Edit;

/**
 * Interceptor class for @see \Magento\Backend\Block\System\Store\Edit
 */
class Interceptor extends \Magento\Backend\Block\System\Store\Edit implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\Block\Widget\Context $context, \Magento\Framework\Registry $registry, array $data = [], ?\Magento\Framework\Serialize\SerializerInterface $serializer = null)
    {
        $this->___init();
        parent::__construct($context, $registry, $data, $serializer);
    }

    /**
     * {@inheritdoc}
     */
    public function addButton($buttonId, $data, $level = 0, $sortOrder = 0, $region = 'toolbar')
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'addButton');
        return $pluginInfo ? $this->___callPlugins('addButton', func_get_args(), $pluginInfo) : parent::addButton($buttonId, $data, $level, $sortOrder, $region);
    }
}
