<?php
/**
 *
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\RewardPoints\Controller\Adminhtml\Reward\Rule;

class Edit extends \MageWorx\RewardPoints\Controller\Adminhtml\Reward\Rule
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * Edit constructor.
     *
     * @param \Magento\Backend\App\Action\Context $context
     * @param \MageWorx\RewardPoints\Model\RuleFactory $ruleFactory
     * @param \MageWorx\RewardPoints\Api\RuleRepositoryInterface $ruleRepository
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Framework\Stdlib\DateTime\Filter\Date $dateFilter
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \MageWorx\RewardPoints\Model\RuleFactory $ruleFactory,
        \MageWorx\RewardPoints\Api\RuleRepositoryInterface $ruleRepository,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\Stdlib\DateTime\Filter\Date $dateFilter,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
        parent::__construct($context, $ruleFactory, $ruleRepository, $coreRegistry, $dateFilter, $logger);
        $this->resultPageFactory = $resultPageFactory;
    }

    /**
     * @return void
     */
    public function execute()
    {
        $model = $this->_initRule();


        if ($model->getRuleId()) {

//            if (!$model->getRuleId()) {
//                $this->messageManager->addErrorMessage(__('This rule no longer exists.'));
//                $this->_redirect('mageworx_rewardpoints/*');
//
//                return;
//            }

            $model->getConditions()->setFormName('mageworx_rewardpoints_rule_form');
            $model->getConditions()->setJsFormObject(
                $model->getConditionsFieldSetId($model->getConditions()->getFormName())
            );
            $model->getActions()->setFormName('mageworx_rewardpoints_rule_form');
            $model->getActions()->setJsFormObject(
                $model->getActionsFieldSetId($model->getActions()->getFormName())
            );
        }

        // set entered data if was error when we do save
        $data = $this->_session->getPageData(true);
        if (!empty($data)) {
            $model->addData($data);
        }

        $this->_initAction();

        $this->_addBreadcrumb(
            $model->getId() ? __('Edit Rule') : __('New Rule'),
            $model->getId() ? __('Edit Rule') : __('New Rule')
        );

        $this->_view->getPage()->getConfig()->getTitle()->prepend(
            $model->getRuleId() ? $model->getName() : __('New Reward Points Rule')
        );
        $this->_view->renderLayout();
    }
}
