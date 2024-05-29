<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\RewardPoints\Block\Adminhtml\Reward\Rule\Edit\Tab;

/**
 * Class Actions - we needed to modify the controller link.
 *
 * @package MageWorx\RewardPoints\Block\Adminhtml\Reward\Rule\Edit\Tab
 */
class Actions extends \Magento\SalesRule\Block\Adminhtml\Promo\Quote\Edit\Tab\Actions
{
    /**
     * {@inheritdoc}
     */
    protected function _prepareForm()
    {
        $model = $this->_coreRegistry->registry(\MageWorx\RewardPoints\Model\RegistryConstants::CURRENT_REWARD_RULE);
        $form  = $this->addTabToForm($model, 'actions_fieldset', 'mageworx_rewardpoints_rule_form');
        $this->setForm($form);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    protected function addTabToForm($model, $fieldsetId = 'actions_fieldset', $formName = 'sales_rule_form')
    {
        $actionsFieldSetId = $model->getActionsFieldSetId($formName);

        $newChildUrl = $this->getUrl(
            'mageworx_rewardpoints/reward_rule/newActionHtml/form/' . $actionsFieldSetId,
            ['form_namespace' => $formName]
        );

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('rule_');

        $renderer = $this->_rendererFieldset->setTemplate(
            'Magento_CatalogRule::promo/fieldset.phtml'
        )->setNewChildUrl(
            $newChildUrl
        )->setFieldSetId(
            $actionsFieldSetId
        )->setNameInLayout('mageworx_rewardpoints_fieldset_promo');

        $fieldset = $form->addFieldset(
            $fieldsetId,
            [
                'legend' => __(
                    'Apply the rule only to cart items matching the following conditions ' .
                    '(leave blank for all items).'

                ),
                'comment' => __("If the action conditions are empty - the price will be calculated from the cart subtotal with discount.") . ' ' .
                    __("If the action conditions exist and include the valid items - the amount will be calculated using the valid items' price and qty.") . ' ' .
                    __("If the action conditions exist and don't include the valid items  - the rule will be ignored.")
            ]
        );

        $fieldset->setRenderer($renderer);

        $field = $fieldset->addField(
            'actions',
            'text',
            [
                'name'           => 'apply_to',
                'label'          => __('Apply To'),
                'title'          => __('Apply To'),
                'required'       => true,
                'data-form-part' => $formName,
            ]
        );

        $field->setRule(
            $model
        )->setRenderer(
            $this->_ruleActions
        );

        $this->_eventManager->dispatch(
            'adminhtml_block_mageworx_rewardpoints_actions_prepareform',
            ['form' => $form]
        );


        $this->setActionFormName($model->getActions(), $formName);
        $form->setValues($model->getData());

        if ($model->isReadonly()) {
            foreach ($fieldset->getElements() as $element) {
                $element->setReadonly(true, true);
            }
        }

        return $form;
    }

    /**
     * Handles addition of form name to action and its actions.
     *
     * @param \Magento\Rule\Model\Condition\AbstractCondition $actions
     * @param string $formName
     * @return void
     */
    private function setActionFormName(\Magento\Rule\Model\Condition\AbstractCondition $actions, $formName)
    {
        $actions->setFormName($formName);
        if (is_array($actions->getActions())) {
            foreach ($actions->getActions() as $condition) {
                $this->setActionFormName($condition, $formName);
            }
        }
    }
}
