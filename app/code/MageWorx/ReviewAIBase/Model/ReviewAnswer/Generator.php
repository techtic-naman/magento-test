<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types = 1);

namespace MageWorx\ReviewAIBase\Model\ReviewAnswer;

use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Review\Model\Rating\Option\VoteFactory;
use Magento\Review\Model\ResourceModel\Rating\Option\Vote\CollectionFactory as VoteCollectionFactory;
use Magento\Review\Model\ResourceModel\Review;
use Magento\Review\Model\ReviewFactory;
use Magento\Store\Model\StoreManagerInterface;
use MageWorx\OpenAI\Api\OptionsInterface;
use MageWorx\OpenAI\Api\OptionsInterfaceFactory as OptionsFactory;
use MageWorx\OpenAI\Api\ResponseInterface;
use MageWorx\ReviewAIBase\Api\ReviewAnswerGeneratorInterface;
use MageWorx\ReviewAIBase\Helper\Config as ConfigHelper;
use Psr\Log\LoggerInterface;

class Generator implements ReviewAnswerGeneratorInterface
{
    const DEFAULT_MAX_TOKENS = 400;

    protected Review                $reviewResource;
    protected ReviewFactory         $reviewFactory;
    protected ConfigHelper          $config;
    protected LoggerInterface       $logger;
    protected ReviewAnswerProcessor $reviewAnswerProcessor;
    protected OptionsFactory        $optionsFactory;
    protected Json                  $jsonSerializer;
    protected StoreManagerInterface $storeManager;
    protected VoteFactory           $voteFactory;
    protected VoteCollectionFactory $voteCollectionFactory;

    public function __construct(
        Review                $reviewResource,
        ReviewFactory         $reviewFactory,
        ReviewAnswerProcessor $reviewAnswerProcessor,
        OptionsFactory        $optionsFactory,
        ConfigHelper          $config,
        Json                  $jsonSerializer,
        StoreManagerInterface $storeManager,
        VoteFactory           $voteFactory,
        VoteCollectionFactory $voteCollectionFactory,
        LoggerInterface       $logger
    ) {
        $this->reviewResource        = $reviewResource;
        $this->reviewFactory         = $reviewFactory;
        $this->reviewAnswerProcessor = $reviewAnswerProcessor;
        $this->optionsFactory        = $optionsFactory;
        $this->config                = $config;
        $this->jsonSerializer        = $jsonSerializer;
        $this->storeManager          = $storeManager;
        $this->logger                = $logger;
        $this->voteFactory           = $voteFactory;
        $this->voteCollectionFactory = $voteCollectionFactory;
    }

    public function generateForReviewById(int $reviewId): string
    {
        $review = $this->reviewFactory->create();
        $this->reviewResource->load($review, $reviewId);

        if (!$review->getId()) {
            throw new NoSuchEntityException(__('Review with ID %1 does not exist', $reviewId));
        }

        $storeId   = (int)$review->getStoreId();
        $productId = (int)$review->getEntityPkValue();

        $content = $this->getPrompt($storeId);
        $context = $this->getContext($review);
        $options = $this->getOptions($storeId);

        $responseContent = $this->generateAnswer($content, $context, $options);

        return $responseContent;
    }

    /**
     * @param string $content
     * @param array $context
     * @param OptionsInterface $options
     * @return string
     * @throws LocalizedException
     */
    protected function generateAnswer(
        string           $content,
        array            $context,
        OptionsInterface $options
    ): string {
        $response = $this->sendDataToAPI($content, $context, $options);

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
     * Send data to API or perform other actions
     *
     * @param string $content
     * @param array $context
     * @param OptionsInterface $options
     * @return ResponseInterface|null
     * @throws LocalizedException
     */
    protected function sendDataToAPI(
        string           $content,
        array            $context,
        OptionsInterface $options
    ): ?ResponseInterface {
        try {
            return $this->reviewAnswerProcessor->execute($content, $context, $options);
        } catch (LocalizedException $localizedException) {
            $this->logger->error($localizedException->getLogMessage());
            throw $localizedException;
        }
    }

    /**
     * Prompt to generate review summary based on context
     *
     * @param int $storeId
     * @return string
     */
    protected function getPrompt(int $storeId): string
    {
        return $this->config->getContentFoAnswerOnReview($storeId);
    }

    /**
     * Formatted customer reviews in context
     *
     * @param int $productId
     * @param int $storeId
     * @return array
     */
    protected function getContext(\Magento\Review\Model\Review $review): array
    {
        $reviewContext                  = [];
        $reviewContext['review_text']   = $review->getDetail();
        $reviewContext['customer_name'] = $review->getNickname();
        $reviewContext['review_title']  = $review->getTitle();

        $rating = $this->getReviewRatingsAsPercentages((int)$review->getId());
        if (!empty($rating)) {
            $reviewContext['review_rating'] = $rating;
        }

        $storeContext = [];
        try {
            $storeContext['store_name'] = $this->storeManager->getStore($review->getStoreId())->getName();
        } catch (NoSuchEntityException $e) {
            $this->logger->warning($e->getMessage());
        }

        $context[] = $this->jsonSerializer->serialize($reviewContext);
        $context[] = $this->jsonSerializer->serialize($storeContext);

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
     * Get review ratings as percentages
     *
     * @param int $reviewId The ID of the review
     * @return array An array of review ratings as percentages
     */
    protected function getReviewRatingsAsPercentages(int $reviewId): array
    {
        $votesCollection = $this->voteCollectionFactory->create()
                                                       ->setReviewFilter($reviewId)
                                                       ->addRatingInfo()
                                                       ->load();

        $ratings = [];
        foreach ($votesCollection as $vote) {
            $ratings[$vote->getRatingCode()] = round((int)$vote->getPercent(), 2);
        }

        return $ratings;
    }

}
