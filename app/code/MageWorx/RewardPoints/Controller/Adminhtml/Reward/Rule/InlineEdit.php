<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\RewardPoints\Controller\Adminhtml\Reward\Rule;

use Magento\Backend\App\Action\Context;
use MageWorx\RewardPoints\Api\RuleRepositoryInterface as RuleRepository;
use Magento\Framework\Controller\Result\JsonFactory;
use MageWorx\RewardPoints\Api\Data\RuleInterface;


class InlineEdit extends \Magento\Backend\App\Action
{
    /**
     * Authorization level of a basic admin session
     */
    const ADMIN_RESOURCE = 'MageWorx_RewardPoints::rule';

    /**
     * @var RuleRepository
     */
    protected $ruleRepository;

    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $jsonFactory;

    /**
     * @var \Magento\Framework\DataObjectFactory
     */
    protected $dataObjectFactory;

    /**
     * @var \Psr\Log\LoggerInterface $logger
     */
    protected $logger;

    /**
     * InlineEdit constructor.
     *
     * @param Context $context
     * @param RuleRepository $ruleRepository
     * @param JsonFactory $jsonFactory
     * @param \Magento\Framework\DataObjectFactory $dataObjectFactory
     */
    public function __construct(
        Context $context,
        RuleRepository $ruleRepository,
        JsonFactory $jsonFactory,
        \Magento\Framework\DataObjectFactory $dataObjectFactory,
        \Psr\Log\LoggerInterface $logger
    ) {
        parent::__construct($context);
        $this->ruleRepository    = $ruleRepository;
        $this->jsonFactory       = $jsonFactory;
        $this->dataObjectFactory = $dataObjectFactory;
    }

    /**
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Json $resultJson */
        $resultJson = $this->jsonFactory->create();
        $error      = false;
        $messages   = [];

        $postItems = $this->getRequest()->getParam('items', []);
        if (!($this->getRequest()->getParam('isAjax') && count($postItems))) {
            return $resultJson->setData(
                [
                    'messages' => [__('Please, correct the sent data.')],
                    'error'    => true,
                ]
            );
        }

        foreach (array_keys($postItems) as $ruleId) {

            try {
                /** @var \MageWorx\RewardPoints\Model\Rule $model */
                $model = $this->ruleRepository->getRuleById($ruleId);
                $data  = array_merge($model->getData(), $postItems[$ruleId]);

                $validateResult = $model->validateData($this->dataObjectFactory->create($data));

                if ($validateResult !== true) {
                    $error = true;

                    foreach ($validateResult as $errorMessage) {
                        $messages[] = $errorMessage;
                    }
                }

                $model->loadPost($data);
                $this->ruleRepository->saveRule($model);

            } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
                $messages[] = __('The rule (ID: %1) is not exists', $ruleId);
                $error      = true;
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $messages[] = $this->getErrorWithRuleId($model, $e->getMessage());
                $error      = true;
            } catch (\RuntimeException $e) {
                $messages[] = $this->getErrorWithRuleId($model, $e->getMessage());
                $error      = true;
            } catch (\Exception $e) {
                $messages[] = $this->getErrorWithRuleId(
                    $model,
                    __('Something went wrong while saving the rule.')
                );
                $error      = true;
            }
        }

        return $resultJson->setData(
            [
                'messages' => $messages,
                'error'    => $error
            ]
        );
    }

    /**
     * Add page title to error message
     *
     * @param PageInterface $rule
     * @param string $errorText
     * @return string
     */
    protected function getErrorWithRuleId(RuleInterface $rule, $errorText)
    {
        return '[Rule ID: ' . $rule->getRuleId() . '] ' . $errorText;
    }
}