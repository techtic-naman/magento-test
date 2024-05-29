<?php
namespace MageWorx\ReviewAIBase\Block\Adminhtml\Config\Frontend\TextareaWithPresets;

/**
 * Interceptor class for @see \MageWorx\ReviewAIBase\Block\Adminhtml\Config\Frontend\TextareaWithPresets
 */
class Interceptor extends \MageWorx\ReviewAIBase\Block\Adminhtml\Config\Frontend\TextareaWithPresets implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\Block\Template\Context $context, \MageWorx\OpenAI\Model\ResourceModel\Presets\Preset\CollectionFactory $presetCollectionFactory, \MageWorx\OpenAI\Model\Presets\PresetGroupFactory $presetGroupFactory, \MageWorx\OpenAI\Model\ResourceModel\Presets\PresetGroup $presetGroupResource, array $data = [])
    {
        $this->___init();
        parent::__construct($context, $presetCollectionFactory, $presetGroupFactory, $presetGroupResource, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function render(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'render');
        return $pluginInfo ? $this->___callPlugins('render', func_get_args(), $pluginInfo) : parent::render($element);
    }
}
