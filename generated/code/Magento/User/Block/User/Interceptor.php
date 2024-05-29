<?php
namespace Magento\User\Block\User;

/**
 * Interceptor class for @see \Magento\User\Block\User
 */
class Interceptor extends \Magento\User\Block\User implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\Block\Widget\Context $context, \Magento\User\Model\ResourceModel\User $resourceModel, array $data = [])
    {
        $this->___init();
        parent::__construct($context, $resourceModel, $data);
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
