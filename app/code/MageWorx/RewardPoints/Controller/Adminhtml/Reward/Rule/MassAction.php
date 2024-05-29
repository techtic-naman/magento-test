<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\RewardPoints\Controller\Adminhtml\Reward\Rule;

use Magento\Framework\Exception\LocalizedException;
use MageWorx\RewardPoints\Controller\Adminhtml\Reward\Rule as RuleController;
use Magento\Backend\App\Action\Context;
use MageWorx\RewardPoints\Model\RuleFactory;
use Magento\Framework\Registry;
use Magento\Ui\Component\MassAction\Filter;
use MageWorx\RewardPoints\Model\ResourceModel\Rule\CollectionFactory;
use MageWorx\RewardPoints\Model\Rule as RuleModel;

abstract class MassAction extends RuleController
{
    /**
     * @var Filter
     */
    protected $filter;

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * MassAction constructor.
     *
     * @param Context $context
     * @param RuleFactory $ruleFactory
     * @param \MageWorx\RewardPoints\Api\RuleRepositoryInterface $ruleRepository
     * @param Registry $coreRegistry
     * @param \Magento\Framework\Stdlib\DateTime\Filter\Date $dateFilter
     * @param \Psr\Log\LoggerInterface $logger
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \MageWorx\RewardPoints\Model\RuleFactory $ruleFactory,
        \MageWorx\RewardPoints\Api\RuleRepositoryInterface $ruleRepository,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\Stdlib\DateTime\Filter\Date $dateFilter,
        \Psr\Log\LoggerInterface $logger,
        Filter $filter,
        CollectionFactory $collectionFactory
    ) {
        parent::__construct($context, $ruleFactory, $ruleRepository, $coreRegistry, $dateFilter, $logger);
        $this->filter            = $filter;
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * @param RuleModel $rule
     * @return mixed
     */
    abstract protected function executeAction(RuleModel $rule);

    /**
     * @param int $size
     * @return \Magento\Framework\Phrase
     */
    abstract protected function getSuccessMessage($size);


    /**
     * @return \Magento\Framework\Phrase
     */
    abstract protected function getErrorMessage();

    /**
     * execute action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        try {
            $collection     = $this->filter->getCollection($this->collectionFactory->create());
            $collectionSize = $collection->getSize();
            foreach ($collection as $rule) {
                $model = $this->ruleRepository->getRuleById($rule->getId());
                $this->executeAction($model);
            }
            $this->messageManager->addSuccessMessage($this->getSuccessMessage($collectionSize));
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addExceptionMessage($e, __($this->getErrorMessage()));
        }
        $redirectResult = $this->resultRedirectFactory->create();
        $redirectResult->setPath('mageworx_rewardpoints/*/index');

        return $redirectResult;
    }
}
