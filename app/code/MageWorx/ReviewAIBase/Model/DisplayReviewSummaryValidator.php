<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types = 1);

namespace MageWorx\ReviewAIBase\Model;

use MageWorx\ReviewAIBase\Api\DisplayReviewSummaryValidatorInterface;
use Magento\Review\Model\Review;
use Magento\Review\Model\ResourceModel\Review\CollectionFactory as ReviewCollectionFactory;
use MageWorx\ReviewAIBase\Helper\Config;

class DisplayReviewSummaryValidator implements DisplayReviewSummaryValidatorInterface
{
    protected ReviewCollectionFactory $reviewCollectionFactory;
    protected Config                  $config;

    public function __construct(
        ReviewCollectionFactory $reviewCollectionFactory,
        Config                  $config
    ) {
        $this->reviewCollectionFactory = $reviewCollectionFactory;
        $this->config                  = $config;
    }

    /**
     * Check if the product's review summary should be displayed.
     *
     * @param int $productId
     * @return bool
     */
    public function validate(int $productId): bool
    {
        $collection = $this->reviewCollectionFactory->create()
                                                    ->addStatusFilter(Review::STATUS_APPROVED)
                                                    ->addEntityFilter('product', $productId)
                                                    ->addFieldToSelect(['review_id'])
                                                    ->addRateVotes();

        // Calculate average rating and count reviews
        $averageRating = 0;
        $ratingsSum    = 0;
        $reviewsCount  = 0;

        if ($collection->getSize() > 0) {
            foreach ($collection as $review) {
                $ratingsSum += $this->getAverageRatingFromReview($review);
                $reviewsCount++;
            }
            $averageRating = $ratingsSum / $reviewsCount;
        }

        return $averageRating >= $this->getMinRating() && $reviewsCount >= $this->getMinSize();
    }

    /**
     * Get average rating for a review.
     *
     * @param Review $review
     * @return float
     */
    protected function getAverageRatingFromReview(Review $review): float
    {
        $votesCollection = $review->getRatingVotes();
        if ($votesCollection && $votesCollection->getSize() > 0) {
            $sum = 0;
            foreach ($votesCollection as $vote) {
                $sum += $vote->getPercent();
            }
            return $sum / $votesCollection->getSize() * 5 / 100;
        }
        return 0;
    }

    /**
     * Minimum average rating required to display summary.
     *
     * @return float
     */
    protected function getMinRating(): float
    {
        return $this->config->getLimitMinRating();
    }

    /**
     * Minimum number of reviews required to display summary.
     *
     * @return int
     */
    protected function getMinSize(): int
    {
        return $this->config->getLimitMinReviews();
    }
}
