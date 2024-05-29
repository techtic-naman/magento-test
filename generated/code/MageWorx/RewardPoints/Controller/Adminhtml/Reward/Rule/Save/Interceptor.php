<?php
namespace MageWorx\RewardPoints\Controller\Adminhtml\Reward\Rule\Save;

/**
 * Interceptor class for @see \MageWorx\RewardPoints\Controller\Adminhtml\Reward\Rule\Save
 */
class Interceptor extends \MageWorx\RewardPoints\Controller\Adminhtml\Reward\Rule\Save implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\App\Action\Context $context, \MageWorx\RewardPoints\Model\RuleFactory $ruleFactory, \MageWorx\RewardPoints\Api\RuleRepositoryInterface $ruleRepository, \Magento\Framework\Registry $coreRegistry, \Magento\Framework\Stdlib\DateTime\Filter\Date $dateFilter, \Psr\Log\LoggerInterface $logger, \Magento\Framework\DataObjectFactory $dataObjectFactory)
    {
        $this->___init();
        parent::__construct($context, $ruleFactory, $ruleRepository, $coreRegistry, $dateFilter, $logger, $dataObjectFactory);
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
