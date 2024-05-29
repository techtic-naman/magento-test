<?php
namespace MageWorx\SocialProofBase\Controller\Adminhtml\Campaign\Save;

/**
 * Interceptor class for @see \MageWorx\SocialProofBase\Controller\Adminhtml\Campaign\Save
 */
class Interceptor extends \MageWorx\SocialProofBase\Controller\Adminhtml\Campaign\Save implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\App\Action\Context $context, \Magento\Framework\Controller\ResultFactory $resultFactory, \Psr\Log\LoggerInterface $logger, \MageWorx\SocialProofBase\Api\CampaignRepositoryInterface $campaignRepository, \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor, \MageWorx\SocialProofBase\Api\Data\CampaignInterfaceFactory $campaignFactory, \MageWorx\SocialProofBase\Helper\Campaign\Rules $rulesHelper, \Magento\Framework\Serialize\Serializer\Json $serializerJson)
    {
        $this->___init();
        parent::__construct($context, $resultFactory, $logger, $campaignRepository, $dataPersistor, $campaignFactory, $rulesHelper, $serializerJson);
    }

    /**
     * {@inheritdoc}
     */
    public function execute() : \Magento\Framework\Controller\Result\Redirect
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
