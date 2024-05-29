<?php
/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_Helpdesk
 * @author    Webkul Software Private Limited
 * @copyright Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */
namespace Webkul\Helpdesk\Block\Adminhtml\SupportCenter\Edit;

/**
 * Adminhtml AccordionFaq Addfaq Edit Form
 */
class Form extends \Magento\Backend\Block\Widget\Form\Generic
{
    /**
     * @var \Magento\Store\Model\System\Store
     */
    protected $_systemStore;

    /**
     * @var \Magento\Cms\Model\Wysiwyg\Config
     */
    protected $_wysiwygConfig;

    /**
     * @var \Magento\Cms\Model\ResourceModel\Page\CollectionFactory
     */
    protected $_cmsColl;

    /**
     * @param \Magento\Backend\Block\Template\Context                 $context
     * @param \Magento\Framework\Registry                             $registry
     * @param \Magento\Framework\Data\FormFactory                     $formFactory
     * @param \Magento\Store\Model\System\Store                       $systemStore
     * @param \Magento\Cms\Model\ResourceModel\Page\CollectionFactory $cmsColl
     * @param \Magento\Cms\Model\Wysiwyg\Config                       $wysiwygConfig
     * @param array                                                   $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Store\Model\System\Store $systemStore,
        \Magento\Cms\Model\ResourceModel\Page\CollectionFactory $cmsColl,
        \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig,
        array $data = []
    ) {
        $this->_systemStore = $systemStore;
        $this->_cmsColl = $cmsColl;
        $this->_wysiwygConfig = $wysiwygConfig;
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
        $this->setId('edit_form');
        $this->setTitle(__('Ticket Support Center Information'));
    }

    /**
     * Prepare form
     *
     * @return $this
     */
    protected function _prepareForm()
    {
        $form = $this->_formFactory->create(
            ['data' => [
                        'id' => 'edit_form',
                        'enctype' => 'multipart/form-data',
                        'action' => $this->getData('action'),
                        'method' => 'post']
                    ]
        );

        $scmodel = $this->_coreRegistry->registry('support_center');

        $form->setHtmlIdPrefix('ts_');

        $fieldset = $form->addFieldset(
            'base_fieldset',
            ['legend' => __('Add Support Center'), 'class' => 'fieldset-wide']
        );

        if ($scmodel->getId()) {
            $fieldset->addField('entity_id', 'hidden', ['name' => 'entity_id']);
        }

        $fieldset->addField(
            'name',
            'text',
            [
                'name'  => 'name',
                'label' => __('Support Center Name'),
                'title' => __('Support Center Name'),
                'required' => true
            ]
        );

        $fieldset->addField(
            'description',
            'editor',
            [
                'name'  => 'description',
                'label' => __('Description'),
                'title' => __('Description'),
                'style' => 'height:5em',
                'required' => true,
                'config' => $this->_wysiwygConfig->getConfig()
            ]
        );

        $fieldset->addField(
            'cms_id',
            'multiselect',
            [
                'label' => __('CMS Page'),
                'title' => __('CMS Page'),
                'name' => 'cms_id',
                'required' => true,
                'values' => $this->getCmsPagesList()
            ]
        );

        $fieldset->addField(
            'status',
            'select',
            [
                'label' => __('Status'),
                'title' => __('Status'),
                'name' => 'status',
                'values' => ['1' => __('Enabled'), '2' => __('Disabled')]
            ]
        );
        $form->setValues($scmodel->getData());
        $form->setUseContainer(true);
        $this->setForm($form);
        return parent::_prepareForm();
    }
    
    /**
     * Get before html form
     */
    public function getCmsPagesList()
    {
        $cmsArr = [];
        $pagesColl = $this->_cmsColl->create()->addFieldToFilter('is_active', \Magento\Cms\Model\Page::STATUS_ENABLED);
        foreach ($pagesColl as $key => $value) {
            array_push($cmsArr, ["value" => $value->getIdentifier(), "label" => $value->gettitle()]);
        }
        return $cmsArr;
    }
}
