<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\RewardPoints\Controller\Adminhtml\Reward;


abstract class Rule extends \Magento\Backend\App\Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'MageWorx_RewardPoints::rule';

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\Filter\Date
     */
    protected $dateFilter;

    /**
     * @var \MageWorx\RewardPoints\Model\RuleFactory
     */
    protected $ruleFactory;

    /**
     * @var \MageWorx\RewardPoints\Api\RuleRepositoryInterface
     */
    protected $ruleRepository;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Framework\App\Response\Http\FileFactory $fileFactory
     * @param \Magento\Framework\Stdlib\DateTime\Filter\Date $dateFilter
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \MageWorx\RewardPoints\Model\RuleFactory $ruleFactory,
        \MageWorx\RewardPoints\Api\RuleRepositoryInterface $ruleRepository,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\Stdlib\DateTime\Filter\Date $dateFilter,
        \Psr\Log\LoggerInterface $logger
    ) {
        parent::__construct($context);
        $this->ruleFactory    = $ruleFactory;
        $this->ruleRepository = $ruleRepository;
        $this->registry       = $coreRegistry;
        $this->dateFilter     = $dateFilter;
        $this->logger         = $logger;
    }

    /**
     * @return \MageWorx\RewardPoints\Model\Rule
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function _initRule()
    {
        $id = (int)$this->getRequest()->getParam('id');

        if (!$id && $this->getRequest()->getParam('rule_id')) {
            $id = (int)$this->getRequest()->getParam('rule_id');
        }

        $rule = $id ? $this->ruleRepository->getRuleById($id) : $this->ruleFactory->create();

        $this->registry->register(
            \MageWorx\RewardPoints\Model\RegistryConstants::CURRENT_REWARD_RULE,
            $rule
        );

        return $rule;
    }

    /**
     * Initiate action
     *
     * @return this
     */
    protected function _initAction()
    {
        $this->_view->loadLayout();
        $this->_setActiveMenu('MageWorx_RewardPoints::rule')->_addBreadcrumb(__('Marketing'), __('Reward Points'));

        return $this;
    }
}
