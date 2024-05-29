<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types = 1);

namespace MageWorx\ReviewAIBase\Model;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Review\Model\ResourceModel\Review\Collection as ReviewCollection;
use Magento\Review\Model\ResourceModel\Review\CollectionFactory as ReviewCollectionFactory;
use Magento\Review\Model\Review;
use MageWorx\OpenAI\Api\Data\QueueItemInterface;
use MageWorx\OpenAI\Api\GeneralOpenAIHelperInterface;
use MageWorx\OpenAI\Api\OptionsInterface;
use MageWorx\OpenAI\Api\OptionsInterfaceFactory as OptionsFactory;
use MageWorx\OpenAI\Api\QueueManagementInterface;
use MageWorx\OpenAI\Api\QueueProcessManagementInterface;
use MageWorx\OpenAI\Api\ResponseInterface;
use MageWorx\ReviewAIBase\Api\AdditionalDataProcessorInterface;
use MageWorx\ReviewAIBase\Api\Data\ReviewSummaryInterface;
use MageWorx\ReviewAIBase\Api\ReviewSummaryGeneratorInterface;
use MageWorx\ReviewAIBase\Api\ReviewSummaryLoaderInterface;
use MageWorx\ReviewAIBase\Api\ReviewSummarySaverInterface;
use MageWorx\ReviewAIBase\Api\VariableStorageInterface;
use MageWorx\ReviewAIBase\Helper\Config as ConfigHelper;
use MageWorx\ReviewAIBase\Model\Config\Source\Status;
use MageWorx\ReviewAIBase\Model\ResourceModel\ReviewSummary as ReviewSummaryResource;
use Psr\Log\LoggerInterface;

class ReviewSummaryGenerator implements ReviewSummaryGeneratorInterface
{
    const DEFAULT_MAX_TOKENS = 400;

    protected ReviewSummaryProcessor          $reviewSummaryProcessor;
    protected ReviewCollectionFactory         $reviewCollectionFactory;
    protected ReviewSummarySaverInterface     $reviewSummarySaver;
    protected OptionsFactory                  $optionsFactory;
    protected LoggerInterface                 $logger;
    protected ConfigHelper                    $config;
    protected array                           $additionalDataProcessors;
    protected ProductRepositoryInterface      $productRepository;
    protected QueueManagementInterface        $queueManagement;
    protected QueueProcessManagementInterface $queueProcessManagement;
    protected GeneralOpenAIHelperInterface    $generalHelper;
    protected ReviewSummaryResource           $reviewSummaryResource;
    protected ReviewSummaryLoaderInterface    $reviewSummaryLoader;
    protected VariableStorageInterface        $variableStorage;

    public function __construct(
        ReviewSummaryProcessor          $reviewSummaryProcessor,
        ReviewCollectionFactory         $reviewCollectionFactory,
        ReviewSummarySaverInterface     $reviewSummarySaver,
        ReviewSummaryLoaderInterface    $reviewSummaryLoader,
        ReviewSummaryResource           $reviewSummaryResource,
        OptionsFactory                  $optionsFactory,
        ProductRepositoryInterface      $productRepository,
        QueueManagementInterface        $queueManagement,
        QueueProcessManagementInterface $queueProcessManagement,
        GeneralOpenAIHelperInterface    $generalHelper,
        ConfigHelper                    $config,
        VariableStorageInterface        $variableStorage,
        LoggerInterface                 $logger,
        array                           $additionalDataProcessors = []
    ) {
        $this->reviewSummaryProcessor  = $reviewSummaryProcessor;
        $this->reviewCollectionFactory = $reviewCollectionFactory;
        $this->reviewSummarySaver      = $reviewSummarySaver;
        $this->optionsFactory          = $optionsFactory;
        $this->config                  = $config;
        $this->productRepository       = $productRepository;
        $this->queueManagement         = $queueManagement;
        $this->queueProcessManagement  = $queueProcessManagement;
        $this->generalHelper           = $generalHelper;
        $this->reviewSummaryResource   = $reviewSummaryResource;
        $this->reviewSummaryLoader     = $reviewSummaryLoader;
        $this->variableStorage         = $variableStorage;

        $this->logger                   = $logger;
        $this->additionalDataProcessors = $additionalDataProcessors;
    }

    /**
     * Generate review summary for a product
     *
     * @param int $productId
     * @param int $storeId
     * @return string
     * @throws LocalizedException
     */
    public function generate(int $productId, int $storeId): string
    {
        $content = $this->getPrompt($storeId);
        $context = $this->getContext($productId, $storeId);
        $options = $this->getOptions($storeId);

        $content = $this->appendProductName($content, $productId, $storeId);
        $content = $this->processVariables($content, $storeId);

        $contentLength           = $this->generalHelper->calculateStringLengthInTokens($content);
        $contextLength           = $this->generalHelper->calculateContextLength($context);
        $fullLength              = $contentLength + $contextLength;
        $contextMaxAllowedLength = $this->generalHelper->getMaxModelSupportedContextLength($options->getModel());

        if ($fullLength <= $contextMaxAllowedLength) {
            // Generate using single request
            $responseContent = $this->generateReviewSummary($content, $context, $options);
        } else {
            // Split to parts, generate summary for each one and summarize all in final request
            $responseContent = $this->generateReviewSummaryFromParts(
                $content,
                $context,
                $options,
                $contextMaxAllowedLength,
                $storeId
            );
        }

        // Save Review Summary after generation.
        $this->reviewSummarySaver->saveUpdate($responseContent, $productId, $storeId);

        return $responseContent;
    }

    /**
     * Schedule review summary generation using queue
     * Returns array of queue items
     *
     * @param int $productId
     * @param int $storeId
     * @return array|QueueItemInterface[]
     * @throws InputException
     * @throws LocalizedException
     */
    public function addToQueue(int $productId, int $storeId): array
    {
        $reviewSummaryEntity = $this->updateReviewSummaryStatus($productId, $storeId, Status::STATUS_PENDING);
        if (!$reviewSummaryEntity->getIsEnabled()) {
            $this->updateReviewSummaryStatus($productId, $storeId, Status::STATUS_DISABLED);
            return []; // Available only for enabled products
        }

        $content = $this->getPrompt($storeId);
        $context = $this->getContext($productId, $storeId);
        if (empty($context)) {
            $this->updateReviewSummaryStatus($productId, $storeId, Status::STATUS_PENDING);
            return []; // No reviews for this product
        }

        $options        = $this->getOptions($storeId);
        $callback       = 'review_ai_summary';
        $additionalData = [
            'product_id' => $productId,
            'store_id'   => $storeId
        ];

        $processCode   = 'mageworx-reviewai-generate-review-summary';
        $processType   = 'generate-review-summary';
        $processName   = 'Generate Review Summary';
        $processModule = 'mageworx_reviewai';

        $content = $this->appendProductName($content, $productId, $storeId);

        $contentLength           = $this->generalHelper->calculateStringLengthInTokens($content);
        $contextLength           = $this->generalHelper->calculateContextLength($context);
        $fullLength              = $contentLength + $contextLength;
        $contextMaxAllowedLength = $this->generalHelper->getMaxModelSupportedContextLength($options->getModel());

        if ($fullLength <= $contextMaxAllowedLength) {
            // Single request
            // @TODO: Update after OpenAI 1.2.0 release
            $process   = $this->queueProcessManagement->registerProcess($processName, 1);
            $queueItem = $this->queueManagement->addToQueue(
                $content,
                $options,
                $callback,
                $context,
                $process,
                $additionalData,
                null,
                'review_ai_summary'
            );

            $queuedItems = [$queueItem];
        } else {
            // Split to parts, generate summary for each one and summarize all in final request
            $parts   = $this->generalHelper->splitRequestToParts($content, $context, $contextMaxAllowedLength);
            // @TODO: Update after OpenAI 1.2.0 release
            $process   = $this->queueProcessManagement->registerProcess($processName, 1);
            foreach ($parts as $part) {
                // Add parts on which summary depends
                $queueItems[] = $this->queueManagement->addToQueue(
                    $content,
                    $options,
                    null, // No callback to prevent from processing by the callback queue processor
                    $part,
                    $process,
                    $additionalData,
                    null,
                    'review_ai_summary'
                );
            }

            if (empty($queueItems)) {
                throw new LocalizedException(__('Queue items are empty'));
            }

            // Add summary queue item
            $summaryContent = $this->config->getSummarizeMessage($storeId);
            // We will use ID for creation of dependency records
            $summaryQueueItem = $this->queueManagement->addToQueue(
                $summaryContent,
                $options,
                $callback,
                [], // No context, because we will use result of another requests when them will be ready
                $process,
                $additionalData,
                'review_ai_summary_from_summary',
                'review_ai_summary'
            );

            // Add dependency records
            $this->queueManagement->addDependency($summaryQueueItem, $queueItems);

            $queuedItems = array_merge([$summaryQueueItem], $queueItems);
        }

        return $queuedItems;
    }

    /**
     * @param string $content
     * @param array $context
     * @param OptionsInterface $options
     * @return string
     * @throws LocalizedException
     */
    protected function generateReviewSummary(
        string           $content,
        array            $context,
        OptionsInterface $options
    ): string {
        $response = $this->sendSummaryDataToAPI($content, $context, $options);

        $chatResponse = $response->getChatResponse();
        if (!empty($chatResponse['error'])) {
            $error =
                is_string($chatResponse['error']) ? $chatResponse['error'] : json_encode($chatResponse['error']);
            $this->logger->warning($error);

            return '';
        }

        return $response->getContent();
    }

    /**
     * Generate review summary from parts for a product
     *
     * @param string $content
     * @param array $context
     * @param OptionsInterface $options
     * @param int $contextMaxAllowedLength
     * @return string
     * @throws LocalizedException
     */
    protected function generateReviewSummaryFromParts(
        string           $content,
        array            $context,
        OptionsInterface $options,
        int              $contextMaxAllowedLength,
        int              $storeId
    ): string {
        $parts = $this->generalHelper->splitRequestToParts($content, $context, $contextMaxAllowedLength);

        $partResponse = [];
        foreach ($parts as $partRequest) {
            try {
                $partResponse[] = $this->generateReviewSummary($content, $partRequest, $options);
            } catch (LocalizedException $localizedException) {
                $this->logger->warning($localizedException);
                continue;
            }
        }

        return $this->summarizeParts($partResponse, $options, $storeId);
    }

    /**
     * @param array $parts
     * @param OptionsInterface $options
     * @param int $storeId
     * @return string
     * @throws LocalizedException
     */
    protected function summarizeParts(array $parts, OptionsInterface $options, int $storeId): string
    {
        $message = $this->config->getSummarizeMessage($storeId);
        $message = $this->processVariables($message, $storeId);

        return $this->generateReviewSummary($message, $parts, $options);
    }

    /**
     * Prompt to generate review summary based on context
     *
     * @param int $storeId
     * @return string
     */
    protected function getPrompt(int $storeId): string
    {
        return $this->config->getContent($storeId);
    }

    /**
     * Formatted customer reviews in context
     *
     * @param int $productId
     * @param int $storeId
     * @return array
     */
    protected function getContext(int $productId, int $storeId): array
    {
        $customerReviewsCollection = $this->getCustomerReviewsCollection($productId, $storeId);
        $context                   = $this->generateContextFromReviewsCollection($customerReviewsCollection, $storeId);

        return $context;
    }

    /**
     * @param int $storeId
     * @return OptionsInterface
     * @throws InputException
     */
    protected function getOptions(int $storeId): OptionsInterface
    {
        /** @var OptionsInterface $options */
        $options = $this->optionsFactory->create();

        $options->setModel($this->getOpenAIModel($storeId))
                ->setTemperature($this->getTemperature($storeId))
                ->setNumberOfResultOptions($this->getNumberOfVariants($storeId))
                ->setMaxTokens($this->getMaxTokens($storeId));

        return $options;
    }

    /**
     * Open AI model to use
     *
     * @param int $storeId
     * @return string
     */
    protected function getOpenAIModel(int $storeId): string
    {
        return $this->config->getOpenAiModel($storeId);
    }

    /**
     * Temperature of generation
     *
     * @param int $storeId
     * @return float
     */
    protected function getTemperature(int $storeId): float
    {
        return $this->config->getTemperature($storeId);
    }

    /**
     * NUmber of variants to generate during one request
     *
     * @param int $storeId
     * @return int
     */
    protected function getNumberOfVariants(int $storeId): int
    {
        return 1;
    }

    /**
     * Maximum length of review summary generated (in tokens)
     *
     * @param int $storeId
     * @return int
     */
    protected function getMaxTokens(int $storeId): int
    {
        $limitInTokens = $this->config->getMaxSummaryLength($storeId);
        if (!$limitInTokens) {
            return static::DEFAULT_MAX_TOKENS;
        }

        return (int)$limitInTokens;
    }

    /**
     * @param int $productId
     * @param int $storeId
     * @return ReviewCollection
     */
    protected function getCustomerReviewsCollection(int $productId, int $storeId): ReviewCollection
    {
        return $this->reviewCollectionFactory
            ->create()
            ->addStoreFilter($storeId)
            ->addStatusFilter(Review::STATUS_APPROVED)
            ->addEntityFilter('product', $productId)
            ->setDateOrder()
            ->addRateVotes();
    }

    /**
     * Generate context array from reviews
     *
     * @param ReviewCollection $collection
     * @param int $storeId
     * @return array
     */
    protected function generateContextFromReviewsCollection(ReviewCollection $collection, int $storeId): array
    {
        $results = [];

        /** @var Review $review */
        foreach ($collection as $review) {
            $title = $review->getTitle();

            $voteObjects = $review->getRatingVotes() ?: [];

            $votesArray = [];
            foreach ($voteObjects as $vote) {
                $votesArray[] = [
                    'rating_code' => $vote->getRatingCode(),
                    'percent'     => $vote->getPercent()
                ];
            }

            $details = $review->getDetail();
            $date    = $review->getCreatedAt();

            $additionalData = $this->processAdditionalReviewData($review, $storeId);

            $preparedReviewArray = [
                'review_title'   => $title,
                'review_details' => $details,
                'review_votes'   => $votesArray,
                'review_date'    => $date
            ];

            if (!empty($additionalData)) {
                $preparedReviewArray['additional_data'] = $additionalData;
            }

            $results[] = $preparedReviewArray;
        }

        return $results;
    }

    /**
     * Run additional data processor to extend regular review data
     *
     * @param Review $review
     * @param int $storeId
     * @return array
     */
    protected function processAdditionalReviewData(Review $review, int $storeId): array
    {
        $additionalData = [];
        $attributes     = $this->config->getAttributes($storeId);

        foreach ($attributes as $attribute) {
            $processor = $this->additionalDataProcessors[$attribute] ?? null;
            if ($processor instanceof AdditionalDataProcessorInterface) {
                $value = $processor->process($review);
                if (!empty($value)) {
                    $additionalData[$attribute] = $value;
                }
            }
        }

        return $additionalData;
    }

    /**
     * Send summary data to API or perform other actions
     *
     * @param string $content
     * @param array $context
     * @param OptionsInterface $options
     * @return ResponseInterface|null
     * @throws LocalizedException
     */
    protected function sendSummaryDataToAPI(
        string           $content,
        array            $context,
        OptionsInterface $options
    ): ?ResponseInterface {
        try {
            return $this->reviewSummaryProcessor->execute($content, $context, $options);
        } catch (LocalizedException $localizedException) {
            $this->logger->error($localizedException->getLogMessage());
            throw $localizedException;
        }
    }

    /**
     * Append product name to request content if necessary
     *
     * @param string $content
     * @param int $productId
     * @param int $storeId
     * @return string
     */
    protected function appendProductName(string $content, int $productId, int $storeId): string
    {
        if (!$this->config->addProductNameToRequest()) {
            return $content;
        }

        try {
            $product = $this->productRepository->getById($productId, false, $storeId);
            $content .= __(' Product: %1', $product->getName());
        } catch (LocalizedException $localizedException) {
            $this->logger->warning($localizedException);
        }

        return $content;
    }

    /**
     * Updates the status of the review summary for a given product and store
     *
     * @param int $productId The ID of the product
     * @param int $storeId The ID of the store
     * @param int $status The new status for the review summary
     * @return ReviewSummaryInterface The updated review summary entity
     */
    protected function updateReviewSummaryStatus(int $productId, int $storeId, int $status): ReviewSummaryInterface
    {
        $reviewSummaryEntity = $this->reviewSummaryLoader->getByProductIdAndStoreId($productId, $storeId);
        $reviewSummaryEntity->setStatus($status)
                            ->setProductId($productId)
                            ->setStoreId($storeId);

        $this->reviewSummaryResource->save($reviewSummaryEntity);

        return $reviewSummaryEntity;
    }

    /**
     * @param string $content
     * @param int $storeId
     * @return string
     */
    protected function processVariables(string $content, int $storeId): string
    {
        if (stripos($content, '{{') === false) {
            return $content;
        }

        $content = preg_replace_callback(
            '/{{(.*?)}}/',
            function ($matches) use ($storeId) {
                $variable = $matches[1];
                $variable = trim($variable);

                $variableParts = explode(' ', $variable);
                $variableName  = $variableParts[0];

                $variableValue = $this->variableStorage->getGeneralVariableValue($variableName, $storeId);

                return $variableValue;
            },
            $content
        );

        return $content;
    }
}
