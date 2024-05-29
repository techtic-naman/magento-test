<?php
namespace Magento\Review\Block\Adminhtml\Edit;

/**
 * Interceptor class for @see \Magento\Review\Block\Adminhtml\Edit
 */
class Interceptor extends \Magento\Review\Block\Adminhtml\Edit implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\Block\Widget\Context $context, \Magento\Review\Model\ReviewFactory $reviewFactory, \Magento\Review\Helper\Action\Pager $reviewActionPager, \Magento\Framework\Registry $registry, array $data = [])
    {
        $this->___init();
        parent::__construct($context, $reviewFactory, $reviewActionPager, $registry, $data);
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
