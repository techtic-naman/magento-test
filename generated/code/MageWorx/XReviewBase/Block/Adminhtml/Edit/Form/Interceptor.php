<?php
namespace MageWorx\XReviewBase\Block\Adminhtml\Edit\Form;

/**
 * Interceptor class for @see \MageWorx\XReviewBase\Block\Adminhtml\Edit\Form
 */
class Interceptor extends \MageWorx\XReviewBase\Block\Adminhtml\Edit\Form implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Catalog\Model\Product\Attribute\Repository $productAttributeRepository, \Magento\Config\Model\Config\Source\Yesno $yesnoOptions, \Magento\Backend\Block\Template\Context $context, \Magento\Framework\Registry $registry, \Magento\Framework\Data\FormFactory $formFactory, \Magento\Store\Model\System\Store $systemStore, \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository, \Magento\Catalog\Model\ProductFactory $productFactory, \Magento\Review\Helper\Data $reviewData, array $data = [])
    {
        $this->___init();
        parent::__construct($productAttributeRepository, $yesnoOptions, $context, $registry, $formFactory, $systemStore, $customerRepository, $productFactory, $reviewData, $data);
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
