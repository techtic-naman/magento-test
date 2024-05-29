<?php
namespace MageWorx\SocialProofBase\Controller\Adminhtml\Campaign\GetTemplateHtml;

/**
 * Interceptor class for @see \MageWorx\SocialProofBase\Controller\Adminhtml\Campaign\GetTemplateHtml
 */
class Interceptor extends \MageWorx\SocialProofBase\Controller\Adminhtml\Campaign\GetTemplateHtml implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\App\Action\Context $context, \Magento\Framework\Controller\ResultFactory $resultFactory, \Psr\Log\LoggerInterface $logger, \MageWorx\SocialProofBase\Api\CampaignRepositoryInterface $campaignRepository, \MageWorx\SocialProofBase\Model\ResourceModel\Campaign\Template\CollectionFactory $collectionFactory)
    {
        $this->___init();
        parent::__construct($context, $resultFactory, $logger, $campaignRepository, $collectionFactory);
    }

    /**
     * {@inheritdoc}
     */
    public function execute() : \Magento\Framework\Controller\ResultInterface
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
