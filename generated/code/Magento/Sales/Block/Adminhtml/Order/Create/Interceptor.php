<?php
namespace Magento\Sales\Block\Adminhtml\Order\Create;

/**
 * Interceptor class for @see \Magento\Sales\Block\Adminhtml\Order\Create
 */
class Interceptor extends \Magento\Sales\Block\Adminhtml\Order\Create implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\Block\Widget\Context $context, \Magento\Backend\Model\Session\Quote $sessionQuote, array $data = [])
    {
        $this->___init();
        parent::__construct($context, $sessionQuote, $data);
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
