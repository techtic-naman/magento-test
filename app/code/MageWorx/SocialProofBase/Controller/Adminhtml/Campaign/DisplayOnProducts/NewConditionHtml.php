<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace MageWorx\SocialProofBase\Controller\Adminhtml\Campaign\DisplayOnProducts;

use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Rule\Model\Condition\AbstractCondition;
use MageWorx\SocialProofBase\Api\CampaignRepositoryInterface;
use MageWorx\SocialProofBase\Helper\Campaign\Rules as RulesHelper;
use Psr\Log\LoggerInterface;

class NewConditionHtml extends \MageWorx\SocialProofBase\Controller\Adminhtml\Campaign
{
    /**
     * @var RulesHelper
     */
    protected $rulesHelper;

    /**
     * NewConditionHtml constructor.
     *
     * @param Context $context
     * @param ResultFactory $resultFactory
     * @param LoggerInterface $logger
     * @param CampaignRepositoryInterface $campaignRepository
     * @param RulesHelper $rulesHelper
     */
    public function __construct(
        Context $context,
        ResultFactory $resultFactory,
        LoggerInterface $logger,
        CampaignRepositoryInterface $campaignRepository,
        RulesHelper $rulesHelper
    ) {
        parent::__construct($context, $resultFactory, $logger, $campaignRepository);

        $this->rulesHelper = $rulesHelper;
    }

    /**
     * @return void
     */
    public function execute(): void
    {
        $typeArr  = explode('|', str_replace('-', '/', $this->getRequest()->getParam('type')));
        $type     = $typeArr[0];
        $id       = $this->getRequest()->getParam('id');
        $formName = $this->getRequest()->getParam('form_namespace');

        $conditionModel = $this->_objectManager->create($type)
                                               ->setId($id)
                                               ->setType($type)
                                               ->setRule($this->rulesHelper->getDisplayOnProductsRuleModel())
                                               ->setPrefix('conditions');

        if (!empty($typeArr[1])) {
            $conditionModel->setAttribute($typeArr[1]);
        }

        if ($conditionModel instanceof AbstractCondition) {

            $conditionModel->setJsFormObject($this->getRequest()->getParam('form'));
            $conditionModel->setFormName($formName);

            $html = $conditionModel->asHtmlRecursive();
        } else {
            $html = '';
        }

        $this->getResponse()->setBody($html);
    }
}
