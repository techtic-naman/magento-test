<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\RewardPoints\Block\Adminhtml\Reward\Rule\Edit\Tab;

class Labels extends \Magento\Backend\Block\Widget\Form\Generic implements
    \Magento\Ui\Component\Layout\Tabs\TabInterface
{
    /**
     * @var \MageWorx\RewardPoints\Api\RuleRepositoryInterface
     */
    private $ruleRepository;

    /**
     * Labels constructor.
     *
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \MageWorx\RewardPoints\Api\RuleRepositoryInterface $ruleRepository
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \MageWorx\RewardPoints\Api\RuleRepositoryInterface $ruleRepository,
        array $data = []
    ) {
        $this->ruleRepository = $ruleRepository;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * @var string
     */
    protected $_nameInLayout = 'store_view_labels';

    /**
     * {@inheritdoc}
     */
    public function getTabClass()
    {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getTabUrl()
    {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function isAjaxLoaded()
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function getTabLabel()
    {
        return __('Labels');
    }

    /**
     * {@inheritdoc}
     */
    public function getTabTitle()
    {
        return __('Labels');
    }

    /**
     * {@inheritdoc}
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    protected function _prepareForm()
    {
        $rule = $this->_coreRegistry->registry(\MageWorx\RewardPoints\Model\RegistryConstants::CURRENT_REWARD_RULE);

        if (!$rule) {
            $id   = $this->getRequest()->getParam('id');
            $rule = $this->ruleRepository->getById($id);
        }

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('rule_');

        $labels = $rule->getStoreLabels();

        if (!$this->_storeManager->isSingleStoreMode()) {
            $fieldset = $this->createStoreSpecificFieldset($form, $labels);
            if ($rule->isReadonly()) {
                foreach ($fieldset->getElements() as $element) {
                    $element->setReadonly(true, true);
                }
            }
        }

        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * Create store specific fieldset
     *
     * @param \Magento\Framework\Data\Form $form
     * @param array $labels
     * @return \Magento\Framework\Data\Form\Element\Fieldset
     */
    protected function createStoreSpecificFieldset($form, $labels)
    {
        $fieldset = $form->addFieldset(
            'store_labels_fieldset',
            ['legend' => __('Store View Specific Labels'), 'class' => 'store-scope']
        );

        $fieldset->setRenderer(
            $this->getLayout()->createBlock(\Magento\Backend\Block\Store\Switcher\Form\Renderer\Fieldset::class)
        );

        foreach ($this->_storeManager->getWebsites() as $website) {

            $fieldset->addField(
                "w_{$website->getId()}_label",
                'note',
                [
                    'label'               => $website->getName(),
                    'fieldset_html_class' => 'website'
                ]
            );
            foreach ($website->getGroups() as $group) {
                $stores = $group->getStores();
                if (!count($stores)) {
                    continue;
                }
                $fieldset->addField(
                    "g_{$group->getId()}_label",
                    'note',
                    ['label' => $group->getName(), 'fieldset_html_class' => 'store-group']
                );
                foreach ($stores as $store) {
                    $fieldset->addField(
                        "s_{$store->getId()}",
                        'text',
                        [
                            'name'                => 'store_labels[' . $store->getId() . ']',
                            'title'               => $store->getName(),
                            'label'               => $store->getName(),
                            'value'               => isset($labels[$store->getId()]) ? $labels[$store->getId()] : '',
                            'required'            => false,
                            'fieldset_html_class' => 'store',
                            'data-form-part'      => 'mageworx_rewardpoints_rule_form'
                        ]
                    );
                }
            }
        }

        return $fieldset;
    }
}
