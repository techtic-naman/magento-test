<?php
namespace MageWorx\XReviewBase\Controller\Ajax\Vote\Vote;

/**
 * Interceptor class for @see \MageWorx\XReviewBase\Controller\Ajax\Vote\Vote
 */
class Interceptor extends \MageWorx\XReviewBase\Controller\Ajax\Vote\Vote implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\App\Action\Context $context, \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator, \MageWorx\XReviewBase\Model\ResourceModel\Vote\CollectionFactory $voteCollectionFactory, \MageWorx\XReviewBase\Model\ResourceModel\Vote $voteResource, \MageWorx\GeoIP\Helper\Customer $ipDetector, \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory, \Psr\Log\LoggerInterface $logger)
    {
        $this->___init();
        parent::__construct($context, $formKeyValidator, $voteCollectionFactory, $voteResource, $ipDetector, $resultJsonFactory, $logger);
    }

    /**
     * {@inheritdoc}
     */
    public function execute() : ?\Magento\Framework\Controller\Result\Json
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
