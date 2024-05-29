<?php
namespace Mageants\ExtensionVersionInformation\Block\Adminhtml\Extensions;

/**
 * Interceptor class for @see \Mageants\ExtensionVersionInformation\Block\Adminhtml\Extensions
 */
class Interceptor extends \Mageants\ExtensionVersionInformation\Block\Adminhtml\Extensions implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\Block\Template\Context $context, \Magento\Framework\Module\FullModuleList $fullModuleList, \Magento\Framework\HTTP\Client\Curl $curl, \Magento\Framework\Module\ModuleListInterface $moduleList, \Magento\Framework\Module\ModuleResource $moduleResource, \Magento\Store\Model\StoreManagerInterface $storeInterface, \Magento\Framework\Module\Manager $moduleManager, array $data = [])
    {
        $this->___init();
        parent::__construct($context, $fullModuleList, $curl, $moduleList, $moduleResource, $storeInterface, $moduleManager, $data);
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
