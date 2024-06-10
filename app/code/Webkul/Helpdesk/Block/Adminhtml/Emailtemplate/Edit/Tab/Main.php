<?php
/**
 * Webkul Software.
 *
 * @category Webkul
 * @package  Webkul_Helpdesk
 * @author   Webkul
 * @license  https://store.webkul.com/license.html
 */
namespace Webkul\Helpdesk\Block\Adminhtml\Emailtemplate\Edit\Tab;

/**
 * Adminhtml Helpdesk Ticket Status Edit Form
 */
class Main extends \Magento\Backend\Block\Widget\Form\Generic
{
    /**
     * @var \Magento\Store\Model\System\Store
     */
    protected $_systemStore;

    /**
     * @param  \Magento\Backend\Block\Template\Context           $context
     * @param  \Magento\Framework\Registry                       $registry
     * @param  \Magento\Framework\Data\FormFactory               $formFactory
     * @param  \Magento\Variable\Model\VariableFactory           $variableFactory
     * @param  \Magento\Email\Model\Source\Variables             $variables
     * @param  array                                             $data
     * @param  \Magento\Framework\Serialize\Serializer\Json|null $serializer
     * @throws \RuntimeException
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Variable\Model\VariableFactory $variableFactory,
        \Magento\Variable\Model\Source\Variables $variables,
        array $data = [],
        \Magento\Framework\Serialize\Serializer\Json $serializer = null
    ) {
        $this->_variableFactory = $variableFactory;
        $this->_variables = $variables;
        $this->serializer = $serializer ?: \Magento\Framework\App\ObjectManager::getInstance()
            ->get(\Magento\Framework\Serialize\Serializer\Json::class);
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Prepare form
     *
     * @return $this
     */
    protected function _prepareForm()
    {
    /**
     * Form variable name
     *
     * @var \Magento\Framework\Data\Form $form
    */
        $form = $this->_formFactory->create();

        $fieldset = $form->addFieldset(
            'base_fieldset',
            ['legend' => __('Template Information'), 'class' => 'fieldset-wide']
        );

        $templateId = $this->getCurrentEmailTemplate()->getId();

        if (isset($templateId)) {
            $fieldset->addField('entity_id', 'hidden', ['name' => 'entity_id', 'value'=>$templateId]);
        }
        $fieldset->addField(
            'currently_used_for',
            'label',
            [
                'label' => __('Currently Used For'),
                'container_id' => 'currently_used_for',
                'after_element_html' => '<script>require(["prototype"], function () {' .
                (!$this->getCurrentEmailTemplate()->getSystemConfigPathsWhereCurrentlyUsed() ? '$(\'' .
                'currently_used_for' .
                '\').hide(); ' : '') .
                '});</script>'
            ]
        );

        $nameConfig = ['name' => 'template_code', 'label' => __('Template Name'), 'required' => true];
        $this->addFieldToFieldSet($fieldset, 'template_code', 'text', $nameConfig);

        $subjectConfig = ['name' => 'template_subject', 'label' => __('Template Subject'), 'required' => true];
        $this->addFieldToFieldSet($fieldset, 'template_subject', 'text', $subjectConfig);
        $oriTempVarConfig = ['name' => 'orig_template_variables'];
        $this->addFieldToFieldSet($fieldset, 'orig_template_variables', 'hidden', $oriTempVarConfig);
        
        $variablesConfig = ['name' => 'variables', 'value' => $this->serializer->serialize($this->getVariables())];
        $this->addFieldToFieldSet($fieldset, 'variables', 'hidden', $variablesConfig);
        $this->addFieldToFieldSet($fieldset, 'template_variables', 'hidden', ['name' => 'template_variables']);
        $insertVariableButton = $this->getLayout()->createBlock(
            \Magento\Backend\Block\Widget\Button::class,
            '',
            [
                'data' => [
                    'type' => 'button',
                    'label' => __('Insert Variable...'),
                    'onclick' => 'templateControl.openVariableChooser();return false;',
                ]
            ]
        );

        $insVarConfig = ['text' => $insertVariableButton->toHtml(), 'label' => ''];
        $this->addFieldToFieldSet($fieldset, 'insert_variable', 'note', $insVarConfig);
        
        $temTextConfig = [
            'name' => 'template_text',
            'label' => __('Template Content'),
            'title' => __('Template Content'),
            'required' => true,
            'style' => 'height:24em;'
        ];
        $this->addFieldToFieldSet($fieldset, 'template_text', 'textarea', $temTextConfig);
        if (!$this->getCurrentEmailTemplate()->isPlain()) {
            $temStyleConfig = [
                'name' => 'template_styles',
                'label' => __('Template Styles'),
                'container_id' => 'field_template_styles'
            ];
            $this->addFieldToFieldSet($fieldset, 'template_styles', 'textarea', $temStyleConfig);
        }

        if ($templateId) {
            $form->addValues($this->getCurrentEmailTemplate()->getData());
        }

        $values = $this->_backendSession->getData('email_template_form_data', true);
        if ($values) {
            $form->setValues($values);
        }

        $this->setForm($form);
        return parent::_prepareForm();
    }

    /**
     * Get email template
     */
    public function getCurrentEmailTemplate()
    {
        return $this->_coreRegistry->registry('current_email_template');
    }

    /**
     * Retrieve variables to insert into email
     *
     * @return array
     */
    public function getVariables()
    {
        $variables = [];
        $variables[] = $this->_variables->toOptionArray(true);
        $customVariables = [
            "label" => "Place-Holders For HelpDesk",
            "value" => [
                ["label"=>"Ticket Id","value"=>"{{var ticketid}}"],
                ["label"=>"Subject","value"=>"{{var subject}}"],
                ["label"=>"Description/Message","value"=>"{{var query}}"],
                ["label"=>"Thread Description/Message","value"=>"{{var reply}}"],
                ["label"=>"Ticket Tags","value"=>"{{var tags}}"],
                ["label"=>"Ticket Notes","value"=>"{{var notes}}"],
                ["label"=>"Ticket Group","value"=>"{{var group}}"],
                ["label"=>"Ticket Type","value"=>"{{var type}}"],
                ["label"=>"Ticket Status","value"=>"{{var status}}"],
                ["label"=>"Ticket Priority","value"=>"{{var priority}}"],
                ["label"=>"Ticket Source","value"=>"{{var source}}"],
                ["label"=>"Agent Name","value"=>"{{var agent_name}}"],
                ["label"=>"Agent Email","value"=>"{{var agent_email}}"],
                ["label"=>"Customer Name","value"=>"{{var customer_name}}"],
                ["label"=>"Customer Email","value"=>"{{var customer_email}}"],
                ["label"=>"Customer Organization","value"=>"{{var customer_organization}}"]
                ]
            ];
        $variables[] = $customVariables;
        return $variables;
    }

    /**
     * AddFieldToFieldSet
     *
     * @param Magento\Framework\Data\Form\Element\Fieldset $fieldset
     * @param string $elementId
     * @param string $type
     * @param array $config
     */
    public function addFieldToFieldSet(
        $fieldset,
        $elementId,
        $type,
        $config
    ) {
        $fieldset->addField($elementId, $type, $config);
    }
}
