<?php
namespace MageWorx\RewardPoints\Controller\Adminhtml\Reward\Rule\InlineEdit;

/**
 * Interceptor class for @see \MageWorx\RewardPoints\Controller\Adminhtml\Reward\Rule\InlineEdit
 */
class Interceptor extends \MageWorx\RewardPoints\Controller\Adminhtml\Reward\Rule\InlineEdit implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\App\Action\Context $context, \MageWorx\RewardPoints\Api\RuleRepositoryInterface $ruleRepository, \Magento\Framework\Controller\Result\JsonFactory $jsonFactory, \Magento\Framework\DataObjectFactory $dataObjectFactory, \Psr\Log\LoggerInterface $logger)
    {
        $this->___init();
        parent::__construct($context, $ruleRepository, $jsonFactory, $dataObjectFactory, $logger);
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
