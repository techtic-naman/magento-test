<?php
namespace MageWorx\XReviewBase\Block\Adminhtml\Add\Form;

/**
 * Interceptor class for @see \MageWorx\XReviewBase\Block\Adminhtml\Add\Form
 */
class Interceptor extends \MageWorx\XReviewBase\Block\Adminhtml\Add\Form implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\Block\Template\Context $context, \Magento\Framework\Registry $registry, \Magento\Framework\Data\FormFactory $formFactory, \Magento\Store\Model\System\Store $systemStore, \Magento\Review\Helper\Data $reviewData, \Magento\Catalog\Model\Product\Attribute\Repository $productAttributeRepository, \Magento\Config\Model\Config\Source\Yesno $yesnoOptions, array $data = [])
    {
        $this->___init();
        parent::__construct($context, $registry, $formFactory, $systemStore, $reviewData, $productAttributeRepository, $yesnoOptions, $data);
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
