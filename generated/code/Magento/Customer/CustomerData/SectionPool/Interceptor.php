<?php
namespace Magento\Customer\CustomerData\SectionPool;

/**
 * Interceptor class for @see \Magento\Customer\CustomerData\SectionPool
 */
class Interceptor extends \Magento\Customer\CustomerData\SectionPool implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\ObjectManagerInterface $objectManager, \Magento\Customer\CustomerData\Section\Identifier $identifier, array $sectionSourceMap = [])
    {
        $this->___init();
        parent::__construct($objectManager, $identifier, $sectionSourceMap);
    }

    /**
     * {@inheritdoc}
     */
    public function getSectionsData(?array $sectionNames = null, $forceNewTimestamp = false)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getSectionsData');
        return $pluginInfo ? $this->___callPlugins('getSectionsData', func_get_args(), $pluginInfo) : parent::getSectionsData($sectionNames, $forceNewTimestamp);
    }

    /**
     * {@inheritdoc}
     */
    public function getSectionNames()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getSectionNames');
        return $pluginInfo ? $this->___callPlugins('getSectionNames', func_get_args(), $pluginInfo) : parent::getSectionNames();
    }
}
