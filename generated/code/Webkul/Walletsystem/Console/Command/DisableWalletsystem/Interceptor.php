<?php
namespace Webkul\Walletsystem\Console\Command\DisableWalletsystem;

/**
 * Interceptor class for @see \Webkul\Walletsystem\Console\Command\DisableWalletsystem
 */
class Interceptor extends \Webkul\Walletsystem\Console\Command\DisableWalletsystem implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\Setup\ModuleDataSetupInterface $dataSetupFactory, \Magento\Framework\App\ResourceConnection $resource, \Magento\Framework\Module\Manager $moduleManager, \Magento\Eav\Model\Entity\Attribute $entityAttribute, \Magento\Framework\Module\Status $modStatus, \Magento\Catalog\Model\ProductRepository $productRepository, \Magento\Framework\Registry $registry, \Magento\Framework\App\State $appstate, \Magento\Eav\Setup\EavSetupFactory $eavSetupFactory, \Magento\Framework\Setup\Patch\PatchHistoryFactory $patchHistoryFactory)
    {
        $this->___init();
        parent::__construct($dataSetupFactory, $resource, $moduleManager, $entityAttribute, $modStatus, $productRepository, $registry, $appstate, $eavSetupFactory, $patchHistoryFactory);
    }

    /**
     * {@inheritdoc}
     */
    public function run(\Symfony\Component\Console\Input\InputInterface $input, \Symfony\Component\Console\Output\OutputInterface $output)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'run');
        return $pluginInfo ? $this->___callPlugins('run', func_get_args(), $pluginInfo) : parent::run($input, $output);
    }
}
