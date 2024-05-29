<?php
namespace MageWorx\RewardPoints\Block\Adminhtml\Customer\Edit\Tab\RewardPoints\Form;

/**
 * Interceptor class for @see \MageWorx\RewardPoints\Block\Adminhtml\Customer\Edit\Tab\RewardPoints\Form
 */
class Interceptor extends \MageWorx\RewardPoints\Block\Adminhtml\Customer\Edit\Tab\RewardPoints\Form implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\Block\Template\Context $context, \Magento\Framework\Registry $registry, \Magento\Framework\Data\FormFactory $formFactory, \Magento\Store\Model\System\StoreFactory $storeFactory, \Magento\Customer\Model\CustomerRegistry $customerRegistry, \Magento\Config\Model\Config\Source\Yesno $yesnoOptions, \MageWorx\RewardPoints\Helper\Data $helperData, array $data = [])
    {
        $this->___init();
        parent::__construct($context, $registry, $formFactory, $storeFactory, $customerRegistry, $yesnoOptions, $helperData, $data);
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
