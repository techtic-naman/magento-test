<?php
/**
 * Webkul Software.
 *
 * @category Webkul
 * @package  Webkul_Helpdesk
 * @author   Webkul
 * @license  https://store.webkul.com/license.html
 */
namespace Webkul\Helpdesk\Block\Adminhtml\Ticketsmanagement\Tickets\Edit;

/**
 * Adminhtml Helpdesk Tickets Edit Form
 */
class Form extends \Magento\Backend\Block\Widget\Form\Generic
{
    /**
     * @var \Magento\Store\Model\System\Store
     */
    protected $_systemStore;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry             $registry
     * @param \Magento\Framework\Data\FormFactory     $formFactory
     * @param \Magento\Store\Model\System\Store       $systemStore
     * @param array                                   $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Store\Model\System\Store $systemStore,
        array $data = []
    ) {
        $this->_systemStore = $systemStore;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Init form
     *
     * @return void
     */
    protected function _construct()
    {

        parent::_construct();
        $this->setId('tickets_form');
        $this->setTitle(__('Ticket Information'));
    }

    /**
     * Prepare form fields
     *
     * @return \Magento\Backend\Block\Widget\Form
     */
    public function _prepareForm()
    {
    /**
     * Form variable name
     *
     * @var \Magento\Framework\Data\Form $form
    */
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('ts_');

        $baseFieldset = $form->addFieldset(
            'base_fieldset',
            ['legend' => __('Add Ticket'), 'class' => 'fieldset-wide']
        );

        $this->setForm($form);
        return parent::_prepareForm();
    }

    /**
     * Prepare the layout.
     *
     * @return $this
     */
    public function getFormHtml()
    {
        $html = parent::getFormHtml();
        $html .= $this->getLayout()->createBlock(
            \Webkul\Helpdesk\Block\Adminhtml\Ticketsmanagement\Tickets\Edit\FormTemplate::class
        )->toHtml();
        return $html;
    }
}
