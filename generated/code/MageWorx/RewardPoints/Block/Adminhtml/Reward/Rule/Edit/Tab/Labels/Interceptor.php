<?php
namespace MageWorx\RewardPoints\Block\Adminhtml\Reward\Rule\Edit\Tab\Labels;

/**
 * Interceptor class for @see \MageWorx\RewardPoints\Block\Adminhtml\Reward\Rule\Edit\Tab\Labels
 */
class Interceptor extends \MageWorx\RewardPoints\Block\Adminhtml\Reward\Rule\Edit\Tab\Labels implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\Block\Template\Context $context, \Magento\Framework\Registry $registry, \Magento\Framework\Data\FormFactory $formFactory, \MageWorx\RewardPoints\Api\RuleRepositoryInterface $ruleRepository, array $data = [])
    {
        $this->___init();
        parent::__construct($context, $registry, $formFactory, $ruleRepository, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function getForm()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getForm');
        return $pluginInfo ? $this->___callPlugins('getForm', func_get_args(), $pluginInfo) : parent::getForm();
    }
}
