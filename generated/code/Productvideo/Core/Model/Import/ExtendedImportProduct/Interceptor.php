<?php
namespace Productvideo\Core\Model\Import\ExtendedImportProduct;

/**
 * Interceptor class for @see \Productvideo\Core\Model\Import\ExtendedImportProduct
 */
class Interceptor extends \Productvideo\Core\Model\Import\ExtendedImportProduct implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Psr\Log\LoggerInterface $logger)
    {
        $this->___init();
        parent::__construct($logger);
    }

    /**
     * {@inheritdoc}
     */
    public function saveProductEntity(array $entityRowsIn, $entityTypeCode)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'saveProductEntity');
        return $pluginInfo ? $this->___callPlugins('saveProductEntity', func_get_args(), $pluginInfo) : parent::saveProductEntity($entityRowsIn, $entityTypeCode);
    }
}
