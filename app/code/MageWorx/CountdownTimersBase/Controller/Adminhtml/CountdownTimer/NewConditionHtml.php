<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace MageWorx\CountdownTimersBase\Controller\Adminhtml\CountdownTimer;

use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Rule\Model\Condition\AbstractCondition;
use MageWorx\CountdownTimersBase\Api\CountdownTimerRepositoryInterface;
use MageWorx\CountdownTimersBase\Helper\CountdownTimer\Rules as RulesHelper;
use Psr\Log\LoggerInterface;

class NewConditionHtml extends \MageWorx\CountdownTimersBase\Controller\Adminhtml\CountdownTimer
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
     * @param CountdownTimerRepositoryInterface $countdownTimerRepository
     * @param RulesHelper $rulesHelper
     */
    public function __construct(
        Context $context,
        ResultFactory $resultFactory,
        LoggerInterface $logger,
        CountdownTimerRepositoryInterface $countdownTimerRepository,
        RulesHelper $rulesHelper
    ) {
        parent::__construct($context, $resultFactory, $logger, $countdownTimerRepository);

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
                                               ->setRule($this->rulesHelper->getRuleModel())
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
