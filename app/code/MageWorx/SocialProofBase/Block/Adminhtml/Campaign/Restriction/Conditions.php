<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace MageWorx\SocialProofBase\Block\Adminhtml\Campaign\Restriction;

use Magento\Framework\Exception\LocalizedException;
use Magento\Rule\Model\Condition\AbstractCondition;
use MageWorx\SocialProofBase\Api\CampaignRepositoryInterface;
use MageWorx\SocialProofBase\Api\Data\CampaignInterface;
use MageWorx\SocialProofBase\Helper\Campaign\Rules as RulesHelper;
use Magento\Backend\Block\Widget\Form\Renderer\Fieldset as FormRendererFieldset;

class Conditions extends \Magento\Backend\Block\Widget\Form\Generic
{
    /**
     * @var CampaignInterface
     */
    protected $campaign;

    /**
     * @var CampaignRepositoryInterface
     */
    protected $campaignRepository;

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
     * @param CampaignRepositoryInterface $campaignRepository
     * @param \Magento\Rule\Block\Conditions $conditions
     * @param RulesHelper $rulesHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        CampaignRepositoryInterface $campaignRepository,
        \Magento\Rule\Block\Conditions $conditions,
        RulesHelper $rulesHelper,
        array $data = []
    ) {
        parent::__construct($context, $registry, $formFactory, $data);

        $this->campaignRepository = $campaignRepository;
        $this->conditions         = $conditions;
        $this->rulesHelper        = $rulesHelper;
    }

    /**
     * @return Conditions
     * @throws LocalizedException
     */
    protected function _prepareForm(): Conditions
    {
        $conditionsName       = 'restriction_conditions';
        $formName             = 'mageworx_socialproofbase_campaign_form';
        $conditionsFieldSetId = $formName . '_' . $conditionsName . '_fieldset';

        if ($this->getCampaign()) {
            $conditionsFieldSetId .= '_' . $this->getCampaign()->getId();
            $ruleModel            = $this->rulesHelper->getRestrictionRuleModel($this->getCampaign());
        } else {
            $ruleModel = $this->rulesHelper->getRestrictionRuleModel();
        }

        $newChildUrl = $this->getUrl(
            'mageworx_socialproofbase/campaign_restriction/newConditionHtml/form/' . $conditionsFieldSetId,
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
                    'Conditions (don\'t add conditions if campaign can display the activity of any product(s))'
                ),
                'class'  => 'fieldset-wide'
            ]
        )->setRenderer($renderer);

        $fieldset->addField(
            $conditionsName,
            'text',
            [
                'name'           => $conditionsName,
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
     * @return CampaignInterface|null
     * @throws LocalizedException
     */
    protected function getCampaign(): ?CampaignInterface
    {
        if (!is_null($this->campaign)) {
            return $this->campaign;
        }

        $id = $this->getRequest()->getParam(CampaignInterface::CAMPAIGN_ID);

        if (!$id) {
            return null;
        }

        $this->campaign = $this->campaignRepository->getById($id);

        return $this->campaign;
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
