<?php
namespace MageWorx\RewardPointsImportExport\Controller\Adminhtml\RewardPoints\ExportPost;

/**
 * Interceptor class for @see \MageWorx\RewardPointsImportExport\Controller\Adminhtml\RewardPoints\ExportPost
 */
class Interceptor extends \MageWorx\RewardPointsImportExport\Controller\Adminhtml\RewardPoints\ExportPost implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\App\Action\Context $context, \Magento\Framework\App\Response\Http\FileFactory $fileFactory, \MageWorx\RewardPoints\Model\ResourceModel\CustomerBalance\CollectionFactory $balanceCollectionFactory)
    {
        $this->___init();
        parent::__construct($context, $fileFactory, $balanceCollectionFactory);
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'execute');
        return $pluginInfo ? $this->___callPlugins('execute', func_get_args(), $pluginInfo) : parent::execute();
    }

    /**
     * {@inheritdoc}
     */
    public function dispatch(\Magento\Framework\App\RequestInterface $request)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'dispatch');
        return $pluginInfo ? $this->___callPlugins('dispatch', func_get_args(), $pluginInfo) : parent::dispatch($request);
    }
}
