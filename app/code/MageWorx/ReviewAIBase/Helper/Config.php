<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types = 1);

namespace MageWorx\ReviewAIBase\Helper;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Module\Manager as ModuleManager;
use Magento\Store\Model\ScopeInterface;

class Config extends AbstractHelper
{
    const XML_PATH_ENABLED                  = 'review_ai/main/is_enabled';
    const XML_PATH_TITLE                    = 'review_ai/main/title';
    const XML_PATH_DESCRIPTION              = 'review_ai/main/description';
    const XML_PATH_ATTRIBUTES               = 'review_ai/main/attributes';
    const XML_PATH_LIMIT_MIN_REVIEWS        = 'review_ai/main/limit_min_reviews';
    const XML_PATH_LIMIT_MIN_RATING         = 'review_ai/main/limit_min_rating';
    const XML_PATH_CONTENT                  = 'review_ai/main/content';
    const XML_PATH_CONTENT_ANSWER_ON_REVIEW = 'review_ai/main/content_answer_on_review';

    const XML_PATH_MAX_LENGTH     = 'review_ai/main/summary_length';
    const XML_PATH_DESIRED_LENGTH = 'review_ai/main/max_length';

    const XML_PATH_ADD_PRODUCT_NAME_TO_REQUEST = 'review_ai/main/add_product_name_to_request';

    const XML_PATH_OPENAI_MODEL = 'review_ai/ai_configuration/openai_model';
    const XML_PATH_TEMPERATURE  = 'review_ai/ai_configuration/temperature';

    const XML_PATH_REVIEW_SUMMARY_TEXT_COLOR      = 'review_ai/style_settings/review_summary_text_color';
    const XML_PATH_REVIEW_SUMMARY_BG_COLOR        = 'review_ai/style_settings/review_summary_bg_color';
    const XML_PATH_REVIEW_SUMMARY_TEXT_SIZE       = 'review_ai/style_settings/review_summary_text_size';
    const XML_PATH_REVIEW_SUMMARY_HEADER_COLOR    = 'review_ai/style_settings/review_summary_header_color';
    const XML_PATH_REVIEW_SUMMARY_HEADER_BG_COLOR = 'review_ai/style_settings/review_summary_header_bg_color';
    const XML_PATH_REVIEW_SUMMARY_HEADER_SIZE     = 'review_ai/style_settings/review_summary_header_size';
    const XML_PATH_REVIEW_SUMMARY_MAIN_CONTAINER_BG_COLOR = 'review_ai/style_settings/review_summary_main_container_bg_color';

    /**
     * @var ScopeConfigInterface
     */
    protected               $scopeConfig;
    protected ModuleManager $moduleManager;

    public function __construct(
        Context              $context,
        ScopeConfigInterface $scopeConfig,
        ModuleManager        $moduleManager
    ) {
        parent::__construct($context);
        $this->scopeConfig   = $scopeConfig;
        $this->moduleManager = $moduleManager;
    }

    /**
     * Is generation enabled
     *
     * @param int|null $storeId
     * @return bool
     */
    public function isEnabled(?int $storeId = null): bool
    {
        return !empty($this->scopeConfig->getValue('mageworx_xreview', ScopeInterface::SCOPE_STORE, $storeId));
    }

    /**
     * @param int|null $storeId
     * @return bool
     */
    public function isXReviewEnabled(?int $storeId = null): bool
    {
        return $this->moduleManager->isEnabled('MageWorx_XReviewBase');
    }

    /**
     * Summary title
     *
     * @param int|null $storeId
     * @return string
     */
    public function getTitle(?int $storeId = null): string
    {
        return (string)$this->scopeConfig->getValue(self::XML_PATH_TITLE, ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * Summary description
     *
     * @param int|null $storeId
     * @return string
     */
    public function getDescription(?int $storeId = null): string
    {
        return (string)$this->scopeConfig->getValue(self::XML_PATH_DESCRIPTION, ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * Attributes of review, used in generation prompt
     *
     * @param int|null $storeId
     * @return array
     */
    public function getAttributes(?int $storeId = null): array
    {
        $attributes = $this->scopeConfig->getValue(self::XML_PATH_ATTRIBUTES, ScopeInterface::SCOPE_STORE, $storeId);
        if (empty($attributes)) {
            return [];
        }

        if (is_string($attributes)) {
            $attributes = explode(',', $attributes);
        }

        return $attributes;
    }

    /**
     * Minimal number of reviews for which generation will be available.
     *
     * @param int|null $storeId
     * @return int
     */
    public function getLimitMinReviews(?int $storeId = null): int
    {
        return (int)$this->scopeConfig->getValue(self::XML_PATH_LIMIT_MIN_REVIEWS, ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * Minimal average rating of reviews for which generation will be available.
     *
     * @param int|null $storeId
     * @return float
     */
    public function getLimitMinRating(?int $storeId = null): float
    {
        return (float)$this->scopeConfig->getValue(self::XML_PATH_LIMIT_MIN_RATING, ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * Prompt content, which will be used for generation of reviews summary.
     *
     * @param int|null $storeId
     * @return string
     */
    public function getContent(?int $storeId = null): string
    {
        return (string)$this->scopeConfig->getValue(self::XML_PATH_CONTENT, ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * Prompt content, which will be used for generation review summary from another summaries.
     * @TODO: Move to real config value
     *
     * @param int|null $storeId
     * @return string
     */
    public function getSummarizeMessage(?int $storeId = null): string
    {
        return 'I have gathered multiple summaries of customer reviews for our product and I am looking for a
            succinct and insightful summary of these. Could you please provide a comprehensive summary that
            encapsulates the main points and the general sentiment expressed in these summaries? Focus on the following aspects:
Pros and Cons: Outline the main positive and negative points mentioned by customers.
Product Quality: Summarize what customers say about the durability, functionality, and overall build of the product.
Quality of Support: Describe the customer experiences with after-sales support, including response times and resolution effectiveness.
Shipping Details: Provide an overview of customer feedback on shipping efficiency, packaging quality, and delivery experience.
Location-Specific Feedback: If any reviews mention specific locations, include how customer experiences may vary based on geographic regions.
Other Characteristics: Note any additional aspects frequently highlighted by customers, such as usability, design, or value for money.
The summary should be balanced, capturing a range of customer experiences and viewpoints to provide a comprehensive overview of the product based on summary of user reviews.
Keep it max {{max_length}} characters. Format text in HTML.';
    }

    /**
     * Max length of review summary generated by OpenAI
     *
     * @param int|null $storeId
     * @return int
     */
    public function getMaxSummaryLength(?int $storeId = null): int
    {
        return (int)$this->scopeConfig->getValue(self::XML_PATH_MAX_LENGTH, ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * Desired length of review summary generated by OpenAI
     *
     * @param int|null $storeId
     * @return int
     */
    public function getDesiredSummaryLength(?int $storeId = null): int
    {
        return (int)$this->scopeConfig->getValue(self::XML_PATH_DESIRED_LENGTH, ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * Open AI Model
     *
     * @param int|null $storeId
     * @return string
     */
    public function getOpenAiModel(?int $storeId = null): string
    {
        return (string)$this->scopeConfig->getValue(self::XML_PATH_OPENAI_MODEL, ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * Temperature
     *
     * @param int|null $storeId
     * @return float
     */
    public function getTemperature(?int $storeId = null): float
    {
        return (float)$this->scopeConfig->getValue(self::XML_PATH_TEMPERATURE, ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * Is it necessary to add the product name to the request when creating a review summary?
     *
     * @param int|null $storeId
     * @return bool
     */
    public function addProductNameToRequest(?int $storeId = null): bool
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_ADD_PRODUCT_NAME_TO_REQUEST,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Prompt content, which will be used for generation of answer on customers review.
     *
     * @param int|null $storeId
     * @return string
     */
    public function getContentFoAnswerOnReview(?int $storeId = null): string
    {
        return (string)$this->scopeConfig->getValue(
            self::XML_PATH_CONTENT_ANSWER_ON_REVIEW,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * @param int|null $storeId
     * @return string
     */
    public function getReviewSummaryTextColor(?int $storeId = null): string
    {
        return (string)$this->scopeConfig->getValue(
            self::XML_PATH_REVIEW_SUMMARY_TEXT_COLOR,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * @param int|null $storeId
     * @return string
     */
    public function getReviewSummaryBgColor(?int $storeId = null): string
    {
        return (string)$this->scopeConfig->getValue(
            self::XML_PATH_REVIEW_SUMMARY_BG_COLOR,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * @param int|null $storeId
     * @return string
     */
    public function getReviewSummaryTextSize(?int $storeId = null): string
    {
        return (string)$this->scopeConfig->getValue(
            self::XML_PATH_REVIEW_SUMMARY_TEXT_SIZE,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * @param int|null $storeId
     * @return string
     */
    public function getReviewSummaryHeaderColor(?int $storeId = null): string
    {
        return (string)$this->scopeConfig->getValue(
            self::XML_PATH_REVIEW_SUMMARY_HEADER_COLOR,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * @param int|null $storeId
     * @return string
     */
    public function getReviewSummaryHeaderBgColor(?int $storeId = null): string
    {
        return (string)$this->scopeConfig->getValue(
            self::XML_PATH_REVIEW_SUMMARY_HEADER_BG_COLOR,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Get the review summary header size
     *
     * @param int|null $storeId The store ID. If null, the default scope will be used.
     * @return string The size of the review summary header
     */
    public function getReviewSummaryHeaderSize(?int $storeId = null): string
    {
        return (string)$this->scopeConfig->getValue(
            self::XML_PATH_REVIEW_SUMMARY_HEADER_SIZE,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    public function getReviewSummaryMainContainerBgColor(?int $storeId = null): string
    {
        return (string)$this->scopeConfig->getValue(
            self::XML_PATH_REVIEW_SUMMARY_MAIN_CONTAINER_BG_COLOR,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }
}
