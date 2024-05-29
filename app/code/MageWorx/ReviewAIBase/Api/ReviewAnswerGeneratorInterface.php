<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types = 1);

namespace MageWorx\ReviewAIBase\Api;

interface ReviewAnswerGeneratorInterface
{
    /**
     * Generates a string for the review with the given ID.
     *
     * @param int $reviewId The ID of the review
     * @return string The generated string
     */
    public function generateForReviewById(int $reviewId): string;
}
