<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types = 1);

namespace MageWorx\ReviewAIBase\Model;

use Magento\Framework\Exception\LocalizedException;
use MageWorx\OpenAI\Api\ResponseInterface;
use MageWorx\OpenAI\Api\OptionsInterface;
use MageWorx\OpenAI\Api\ResponseInterfaceFactory;
use MageWorx\OpenAI\Api\RequestInterfaceFactory;
use MageWorx\OpenAI\Model\Models\ModelsFactory;
use MageWorx\ReviewAIBase\Exception\InputDataException;

class ReviewSummaryProcessor
{
    protected RequestInterfaceFactory  $requestFactory;
    protected ResponseInterfaceFactory $responseFactory;
    protected ModelsFactory            $modelsFactory;

    public function __construct(
        RequestInterfaceFactory  $requestFactory,
        ResponseInterfaceFactory $responseFactory,
        ModelsFactory            $modelsFactory
    ) {
        $this->requestFactory  = $requestFactory;
        $this->responseFactory = $responseFactory;
        $this->modelsFactory   = $modelsFactory;
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
            throw new InputDataException(__('Review Summary Creation Failed: A template prompt is required to generate a summary. Please go to the module settings and set the default template prompt before proceeding.'));
        }

        if (empty($context)) {
            throw new InputDataException(__('Review Summary Creation Failed: A product must have at least one review to generate a summary.'));
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
