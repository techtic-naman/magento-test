<?php
/**
 *
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\RewardPoints\Controller\Adminhtml\Reward\Rule;

use Magento\Framework\Filter\FilterInput;
use MageWorx\RewardPoints\Model\Rule;

class Save extends \MageWorx\RewardPoints\Controller\Adminhtml\Reward\Rule
{
    /**
     * @var \Magento\Framework\DataObjectFactory
     */
    protected $dataObjectFactory;

    /**
     * Save constructor.
     *
     * @param \Magento\Backend\App\Action\Context $context
     * @param \MageWorx\RewardPoints\Model\RuleFactory $ruleFactory
     * @param \MageWorx\RewardPoints\Api\RuleRepositoryInterface $ruleRepository
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Framework\Stdlib\DateTime\Filter\Date $dateFilter
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\DataObjectFactory $dataObjectFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \MageWorx\RewardPoints\Model\RuleFactory $ruleFactory,
        \MageWorx\RewardPoints\Api\RuleRepositoryInterface $ruleRepository,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\Stdlib\DateTime\Filter\Date $dateFilter,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\DataObjectFactory $dataObjectFactory
    ) {
        parent::__construct($context, $ruleFactory, $ruleRepository, $coreRegistry, $dateFilter, $logger);
        $this->dataObjectFactory = $dataObjectFactory;
    }

    /**
     * Reward rule save action
     *
     * @return void
     */
    public function execute()
    {
        if ($this->getRequest()->getPostValue()) {
            try {
                $this->_eventManager->dispatch(
                    'mageworx_rewardpoints_controller_rule_prepare_save',
                    ['request' => $this->getRequest()]
                );
                $data = $this->getRequest()->getPostValue();

                if ($this->getRequest()->getParam('from_date')) {
                    $data['from_date'] = $this->dateFilter->filter($data['from_date']);
                }

                if ($this->getRequest()->getParam('to_date')) {
                    $data['to_date'] = $this->dateFilter->filter($data['to_date']);
                }

                $id    = $this->getRequest()->getParam('id');
                $model = $id ? $this->ruleRepository->getRuleById($id) : $this->ruleFactory->create();

                $validateResult = $model->validateData($this->dataObjectFactory->create()->setData($data));

                if ($validateResult !== true) {
                    foreach ($validateResult as $errorMessage) {
                        $this->messageManager->addErrorMessage($errorMessage);
                    }
                    $this->_session->setPageData($data);
                    $this->_redirect('mageworx_rewardpoints/*/edit', ['id' => $model->getId()]);

                    return;
                }

                if (isset($data['rule']['conditions'])) {
                    $data['conditions'] = $data['rule']['conditions'];
                }
                if (isset($data['rule']['actions'])) {
                    $data['actions'] = $data['rule']['actions'];
                }
                unset($data['rule']);

                $model->loadPost($data);
                $this->_session->setPageData($model->getData());
                $this->ruleRepository->saveRule($model);

                $this->messageManager->addSuccessMessage(__('You have saved the rule.'));
                $this->_session->setPageData(false);
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('mageworx_rewardpoints/*/edit', ['id' => $model->getId()]);

                    return;
                }
                $this->_redirect('mageworx_rewardpoints/*/');

                return;
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                $id = (int)$this->getRequest()->getParam('rule_id');
                if (!empty($id)) {
                    $this->_redirect('mageworx_rewardpoints/*/edit', ['id' => $id]);
                } else {
                    $this->_redirect('mageworx_rewardpoints/*/new');
                }

                return;
            } catch (\Exception $e) {

                $this->messageManager->addErrorMessage(
                    __('Something went wrong while saving the rule data. Please review the error log.')
                );
                $this->logger->critical($e);
                $this->_session->setPageData($data);
                $this->_redirect('mageworx_rewardpoints/*/edit', ['id' => $this->getRequest()->getParam('rule_id')]);

                return;
            }
        }
        $this->_redirect('mageworx_rewardpoints/*/');
    }
}
