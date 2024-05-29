<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types = 1);

namespace MageWorx\ReviewAIBase\Model\ReviewAnswer;

use Magento\Framework\Exception\LocalizedException;
use MageWorx\OpenAI\Api\OptionsInterface;
use MageWorx\OpenAI\Api\RequestInterfaceFactory;
use MageWorx\OpenAI\Api\ResponseInterface;
use MageWorx\OpenAI\Model\Models\ModelsFactory;
use MageWorx\ReviewAIBase\Exception\InputDataException;

class ReviewAnswerProcessor
{
    protected RequestInterfaceFactory $requestFactory;
    protected ModelsFactory           $modelsFactory;

    public function __construct(
        RequestInterfaceFactory $requestFactory,
        ModelsFactory           $modelsFactory
    ) {
        $this->requestFactory = $requestFactory;
        $this->modelsFactory  = $modelsFactory;
    }

    /**
     * @param string $content
     * @param array $context
     * @param OptionsInterface $options
     * @return ResponseInterface
     * @throws LocalizedException
     */
    public function execute(string $content, array $context, OptionsInterface $options): ResponseInterface
    {
        if (empty($content)) {
            throw new InputDataException(__('Review Answer Creation Failed: A template prompt is required to generate an answer. Please go to the module settings and set the default template prompt before proceeding.'));
        }

        if (empty($context)) {
            throw new InputDataException(__('Review Answer Creation Failed: context required to generate an answer.'));
        }

        $model = $this->modelsFactory->create($options->getModel());

        $request = $this->requestFactory->create();
        $request->setContent($content);
        $request->setContext($context);
        $request->setPath($model->getPath());

        $response = $model->sendRequest($request, $options);

        return $response;
    }
}
