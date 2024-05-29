<?php
namespace Amasty\Storelocator\Controller\Location\Schedule;

/**
 * Interceptor class for @see \Amasty\Storelocator\Controller\Location\Schedule
 */
class Interceptor extends \Amasty\Storelocator\Controller\Location\Schedule implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\View\LayoutFactory $layoutFactory, \Magento\Framework\Controller\Result\RawFactory $resultRawFactory, \Amasty\Storelocator\Model\Repository\LocationRepository $locationRepository, \Magento\Framework\App\RequestInterface $request)
    {
        $this->___init();
        parent::__construct($layoutFactory, $resultRawFactory, $locationRepository, $request);
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'execute');
        return $pluginInfo ? $this->___callPlugins('execute', func_get_args(), $pluginInfo) : parent::execute();
    }
}
