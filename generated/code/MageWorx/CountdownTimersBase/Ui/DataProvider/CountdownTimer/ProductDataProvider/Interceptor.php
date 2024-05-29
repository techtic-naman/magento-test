<?php
namespace MageWorx\CountdownTimersBase\Ui\DataProvider\CountdownTimer\ProductDataProvider;

/**
 * Interceptor class for @see \MageWorx\CountdownTimersBase\Ui\DataProvider\CountdownTimer\ProductDataProvider
 */
class Interceptor extends \MageWorx\CountdownTimersBase\Ui\DataProvider\CountdownTimer\ProductDataProvider implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(string $name, string $primaryFieldName, string $requestFieldName, \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $collectionFactory, array $addFieldStrategies = [], array $addFilterStrategies = [], array $meta = [], array $data = [])
    {
        $this->___init();
        parent::__construct($name, $primaryFieldName, $requestFieldName, $collectionFactory, $addFieldStrategies, $addFilterStrategies, $meta, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getData');
        return $pluginInfo ? $this->___callPlugins('getData', func_get_args(), $pluginInfo) : parent::getData();
    }
}
