<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types = 1);

namespace MageWorx\ReviewAIBase\Controller\Adminhtml\Review;

use Magento\Backend\App\Action;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\NotFoundException;
use MageWorx\ReviewAIBase\Api\ReviewAnswerGeneratorInterface;

class GenerateAnswer extends Action implements HttpPostActionInterface
{
    protected ReviewAnswerGeneratorInterface $reviewAnswerGenerator;

    public function __construct(
        Action\Context                 $context,
        ResultFactory                  $resultFactory,
        ReviewAnswerGeneratorInterface $reviewAnswerGenerator
    ) {
        parent::__construct($context);
        $this->resultFactory         = $resultFactory;
        $this->reviewAnswerGenerator = $reviewAnswerGenerator;
    }

    /**
     * Execute action based on request and return result
     *
     * @return ResultInterface|ResponseInterface
     * @throws NotFoundException
     */
    public function execute()
    {
        $result = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_JSON);

        $reviewId = (int)$this->getRequest()->getParam('review_id');

        try {
            $answer = $this->reviewAnswerGenerator->generateForReviewById($reviewId);
        } catch (\Exception $e) {
            $result->setData(
                [
                    'success' => false,
                    'message' => $e->getMessage()
                ]
            );

            return $result;
        }

        $result->setData(
            [
                'success' => true,
                'answer' => $answer
            ]
        );

        return $result;
    }
}
