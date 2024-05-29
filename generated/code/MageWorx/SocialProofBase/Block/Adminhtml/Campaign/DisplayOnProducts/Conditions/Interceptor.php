<?php
namespace MageWorx\SocialProofBase\Block\Adminhtml\Campaign\DisplayOnProducts\Conditions;

/**
 * Interceptor class for @see \MageWorx\SocialProofBase\Block\Adminhtml\Campaign\DisplayOnProducts\Conditions
 */
class Interceptor extends \MageWorx\SocialProofBase\Block\Adminhtml\Campaign\DisplayOnProducts\Conditions implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\Block\Template\Context $context, \Magento\Framework\Registry $registry, \Magento\Framework\Data\FormFactory $formFactory, \MageWorx\SocialProofBase\Api\CampaignRepositoryInterface $campaignRepository, \Magento\Rule\Block\Conditions $conditions, \MageWorx\SocialProofBase\Helper\Campaign\Rules $rulesHelper, array $data = [])
    {
        $this->___init();
        parent::__construct($context, $registry, $formFactory, $campaignRepository, $conditions, $rulesHelper, $data);
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
