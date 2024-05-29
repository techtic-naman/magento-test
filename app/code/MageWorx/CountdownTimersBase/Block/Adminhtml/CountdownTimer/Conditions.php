<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace MageWorx\CountdownTimersBase\Block\Adminhtml\CountdownTimer;

use Magento\Framework\Exception\LocalizedException;
use Magento\Rule\Model\Condition\AbstractCondition;
use MageWorx\CountdownTimersBase\Api\CountdownTimerRepositoryInterface;
use MageWorx\CountdownTimersBase\Api\Data\CountdownTimerInterface;
use MageWorx\CountdownTimersBase\Helper\CountdownTimer\Rules as RulesHelper;
use Magento\Backend\Block\Widget\Form\Renderer\Fieldset as FormRendererFieldset;

class Conditions extends \Magento\Backend\Block\Widget\Form\Generic
{
    /**
     * @var CountdownTimerInterface
     */
    protected $countdownTimer;

    /**
     * @var CountdownTimerRepositoryInterface
     */
    protected $countdownTimerRepository;

    /**
     * @var \Magento\Rule\Block\Conditions
     */
    protected $conditions;

    /**
     * @var RulesHelper
     */
    protected $rulesHelper;

    /**
     * Conditions constructor.
     *
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param CountdownTimerRepositoryInterface $countdownTimerRepository
     * @param \Magento\Rule\Block\Conditions $conditions
     * @param RulesHelper $rulesHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        CountdownTimerRepositoryInterface $countdownTimerRepository,
        \Magento\Rule\Block\Conditions $conditions,
        RulesHelper $rulesHelper,
        array $data = []
    ) {
        parent::__construct($context, $registry, $formFactory, $data);

        $this->countdownTimerRepository = $countdownTimerRepository;
        $this->conditions               = $conditions;
        $this->rulesHelper              = $rulesHelper;
    }

    /**
     * @return Conditions
     * @throws LocalizedException
     */
    protected function _prepareForm(): Conditions
    {
        $conditionsName       = 'conditions';
        $formName             = 'mageworx_countdowntimersbase_countdown_timer_form';
        $conditionsFieldSetId = $formName . '_' . $conditionsName . '_fieldset';

        if ($this->getCountdownTimer()) {
            $conditionsFieldSetId .= '_' . $this->getCountdownTimer()->getId();
            $ruleModel            = $this->rulesHelper->getRuleModel($this->getCountdownTimer());
        } else {
            $ruleModel = $this->rulesHelper->getRuleModel();
        }

        $newChildUrl = $this->getUrl(
            'mageworx_countdowntimersbase/countdownTimer/newConditionHtml/form/' . $conditionsFieldSetId,
            ['form_namespace' => $formName]
        );

        $renderer = $this->getLayout()->createBlock(FormRendererFieldset::class);
        $renderer
            ->setTemplate(
                'Magento_CatalogRule::promo/fieldset.phtml'
            )->setNewChildUrl(
                $newChildUrl
            )->setFieldSetId(
                $conditionsFieldSetId
            );

        $form     = $this->_formFactory->create();
        $fieldset = $form->addFieldset(
            $conditionsName . '_fieldset',
            [
                'legend' => __(
                    'Conditions (don\'t add conditions if countdown timer can be displayed on any product(s))'
                ),
                'class'  => 'fieldset-wide'
            ]
        )->setRenderer($renderer);

        $fieldset->addField(
            $conditionsName,
            'text',
            [
                'name'           => $conditionsName,
                'label'          => __('Conditions'),
                'title'          => __('Conditions'),
                'required'       => true,
                'data-form-part' => $formName
            ]
        )->setRule(
            $ruleModel
        )->setRenderer(
            $this->conditions
        );

        $form->setValues($ruleModel->getData());
        $this->setConditionFormName($ruleModel->getConditions(), $formName, $conditionsFieldSetId);
        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * @return CountdownTimerInterface|null
     * @throws LocalizedException
     */
    protected function getCountdownTimer(): ?CountdownTimerInterface
    {
        if (!is_null($this->countdownTimer)) {
            return $this->countdownTimer;
        }

        $id = $this->getRequest()->getParam(CountdownTimerInterface::COUNTDOWN_TIMER_ID);

        if (!$id) {
            return null;
        }

        $this->countdownTimer = $this->countdownTimerRepository->getById($id);

        return $this->countdownTimer;
    }

    /**
     * @param AbstractCondition $conditionsModel
     * @param string $formName
     * @param string $jsFormName
     * @return void
     */
    private function setConditionFormName(AbstractCondition $conditionsModel, $formName, $jsFormName): void
    {
        $conditionsModel->setFormName($formName);
        $conditionsModel->setJsFormObject($jsFormName);

        $conditions = $conditionsModel->getConditions();

        if ($conditions && is_array($conditions)) {

            foreach ($conditions as $condition) {
                $this->setConditionFormName($condition, $formName, $jsFormName);
            }
        }
    }
}
