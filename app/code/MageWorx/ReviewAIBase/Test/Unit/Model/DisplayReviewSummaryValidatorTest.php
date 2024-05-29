<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types = 1);

namespace MageWorx\ReviewAIBase\Test\Unit\Model;

use MageWorx\ReviewAIBase\Model\DisplayReviewSummaryValidator;
use Magento\Review\Model\ResourceModel\Review\Collection as ReviewCollection;
use Magento\Review\Model\ResourceModel\Review\CollectionFactory as ReviewCollectionFactory;
use Magento\Review\Model\Review;
use Magento\Review\Model\Rating\Option\Vote;
use Magento\Review\Model\ResourceModel\Rating\Option\Vote\Collection as VoteCollection;
use PHPUnit\Framework\TestCase;
use MageWorx\ReviewAIBase\Helper\Config;

/**
 * Test class for \MageWorx\ReviewAIBase\Model\DisplayReviewSummaryValidator.
 *
 * This class contains unit tests for DisplayReviewSummaryValidator which
 * validate whether a product's review summary should be displayed based on
 * the product's review count and average rating. It tests various scenarios
 * such as having a sufficient number of high-rated reviews, low average rating,
 * and insufficient number of reviews.
 *
 */
class DisplayReviewSummaryValidatorTest extends TestCase
{
    private $validator;
    private $reviewCollectionFactoryMock;
    private $reviewCollectionMock;
    private $voteCollectionMock;
    private $configMock;

    protected function setUp(): void
    {
        $this->reviewCollectionFactoryMock = $this->createMock(ReviewCollectionFactory::class);
        $this->reviewCollectionMock        = $this->createMock(ReviewCollection::class);
        $this->voteCollectionMock          = $this->createMock(VoteCollection::class);
        $this->configMock                  = $this->createMock(Config::class);

        $this->validator = new DisplayReviewSummaryValidator(
            $this->reviewCollectionFactoryMock,
            $this->configMock
        );
    }

    /**
     * Test case for when the validate method should return true.
     * This simulates a scenario where the product has enough reviews
     * with a high average rating.
     */
    public function testValidateReturnsTrue(): void
    {
        // Arrange
        $productId     = 123;
        $reviewsCount  = 150;
        $averageRating = 4.5;

        $this->setUpReviewCollectionMock($productId, $reviewsCount, $averageRating);
        $this->configMock->expects($this->once())
                         ->method('getLimitMinReviews')
                         ->willReturn(100);
        $this->configMock->expects($this->once())
                         ->method('getLimitMinRating')
                         ->willReturn(4.0);

        // Act
        $result = $this->validator->validate($productId);

        // Assert
        $this->assertTrue($result);
    }

    /**
     * Test case for when the validate method should return false due to a low average rating.
     * This simulates the scenario where the product has enough reviews but the average
     * rating is below the threshold.
     */
    public function testValidateReturnsFalseDueToLowAverageRating(): void
    {
        // Arrange
        $productId     = 123;
        $reviewsCount  = 150;
        $averageRating = 3.5; // Below the threshold

        $this->setUpReviewCollectionMock($productId, $reviewsCount, $averageRating);
        $this->configMock->expects($this->never())
                         ->method('getLimitMinReviews');
        $this->configMock->expects($this->once())
                         ->method('getLimitMinRating')
                         ->willReturn(4.0);

        // Act
        $result = $this->validator->validate($productId);

        // Assert
        $this->assertFalse($result);
    }

    /**
     * Test case for when the validate method should return false due to an insufficient number of reviews.
     * This simulates the scenario where the product has a high average rating but not enough reviews.
     */
    public function testValidateReturnsFalseDueToInsufficientNumberOfReviews(): void
    {
        // Arrange
        $productId     = 123;
        $reviewsCount  = 50; // Below the threshold
        $averageRating = 4.5;

        $this->setUpReviewCollectionMock($productId, $reviewsCount, $averageRating);
        $this->configMock->expects($this->once())
                         ->method('getLimitMinReviews')
                         ->willReturn(100);
        $this->configMock->expects($this->once())
                         ->method('getLimitMinRating')
                         ->willReturn(4.0);

        // Act
        $result = $this->validator->validate($productId);

        // Assert
        $this->assertFalse($result);
    }

    /**
     * Sets up the review collection mock used within the tests.
     * It configures the mock to simulate different scenarios based on the product ID,
     * number of reviews, and average rating provided to it.
     *
     * @param int $productId The product ID to simulate.
     * @param int $reviewsCount The number of reviews to simulate.
     * @param float $averageRating The average rating to simulate.
     */
    private function setUpReviewCollectionMock(int $productId, int $reviewsCount, float $averageRating): void
    {
        $this->reviewCollectionFactoryMock->method('create')
                                          ->willReturn($this->reviewCollectionMock);

        $this->reviewCollectionMock->expects($this->once())
                                   ->method('addStatusFilter')
                                   ->with(Review::STATUS_APPROVED)
                                   ->willReturn($this->reviewCollectionMock);

        $this->reviewCollectionMock->expects($this->once())
                                   ->method('addEntityFilter')
                                   ->with('product', $productId)
                                   ->willReturn($this->reviewCollectionMock);

        $this->reviewCollectionMock->expects($this->once())
                                   ->method('getSize')
                                   ->willReturn($reviewsCount);

        // Mocking the average rating calculation
        if ($reviewsCount > 0) {
            $this->reviewCollectionMock->expects($this->once())
                                       ->method('addFieldToSelect')
                                       ->willReturn($this->reviewCollectionMock);

            $this->reviewCollectionMock->expects($this->once())
                                       ->method('addRateVotes')
                                       ->willReturn($this->reviewCollectionMock);

            $reviewMock = $this->getMockBuilder(Review::class)
                               ->disableOriginalConstructor()
                               ->addMethods(['getRatingVotes'])
                               ->getMock();
            $reviewMock->method('getRatingVotes')
                       ->willReturn($this->getRatingVotesMock($averageRating));

            $this->reviewCollectionMock->expects($this->any())
                                       ->method('getItems')
                                       ->willReturn(array_fill(0, $reviewsCount, $reviewMock));
            $this->reviewCollectionMock->expects($this->any())
                                       ->method('getIterator')
                                       ->willReturn(new \ArrayIterator(array_fill(0, $reviewsCount, $reviewMock)));
        }
    }

    /**
     * Creates a mock for the rating votes collection and configures it to return
     * a specified average rating.
     *
     * @param float $averageRating The average rating to be returned by the mock.
     * @return VoteCollection The mocked votes collection object.
     */
    private function getRatingVotesMock(float $averageRating): VoteCollection
    {
        $voteMock = $this->getMockBuilder(Vote::class)
                         ->disableOriginalConstructor()
                         ->addMethods(['getPercent'])
                         ->getMock();

        $voteMock->method('getPercent')->willReturn($averageRating * 20); // Convert 5 star rating value to percent
        $voteMocksArray = [$voteMock];

        $this->voteCollectionMock->expects($this->any())
                                 ->method('getItems')
                                 ->willReturn($voteMocksArray);

        $this->voteCollectionMock->expects($this->atLeastOnce())
                                 ->method('getSize')
                                 ->willReturn(count($voteMocksArray));

        $this->voteCollectionMock->expects($this->any())
                                 ->method('getIterator')
                                 ->willReturn(new \ArrayIterator($voteMocksArray));

        return $this->voteCollectionMock;
    }
}
