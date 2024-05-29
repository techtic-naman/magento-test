<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types = 1);

namespace MageWorx\ReviewAIBase\Test\Unit\Model\ReviewAnswer;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Review\Model\Rating\Option\Vote;
use Magento\Review\Model\Rating\Option\VoteFactory;
use Magento\Review\Model\ResourceModel\Rating\Option\Vote\Collection as VoteCollection;
use Magento\Review\Model\ResourceModel\Rating\Option\Vote\CollectionFactory as VoteCollectionFactory;
use Magento\Review\Model\ResourceModel\Review as ReviewResource;
use Magento\Review\Model\Review;
use Magento\Review\Model\ReviewFactory;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;
use MageWorx\OpenAI\Api\OptionsInterface;
use MageWorx\OpenAI\Api\OptionsInterfaceFactory as OptionsFactory;
use MageWorx\OpenAI\Api\ResponseInterface;
use MageWorx\ReviewAIBase\Helper\Config as ConfigHelper;
use MageWorx\ReviewAIBase\Model\ReviewAnswer\Generator;
use MageWorx\ReviewAIBase\Model\ReviewAnswer\ReviewAnswerProcessor;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class GeneratorTest extends TestCase
{
    /**
     * @var Generator
     */
    private $generator;

    /**
     * @var MockObject|Review
     */
    private $mockReview;

    /**
     * @var MockObject|Vote
     */
    private $mockVote;

    /**
     * @var MockObject|VoteCollection
     */
    private $mockVoteCollection;

    /**
     * @var MockObject|ReviewFactory
     */
    private $mockReviewFactory;

    /**
     * @var MockObject|ReviewResource
     */
    private $mockReviewResource;

    /**
     * @var MockObject|VoteFactory
     */
    private $mockVoteFactory;

    /**
     * @var MockObject|VoteCollectionFactory
     */
    private $mockVoteCollectionFactory;

    /**
     * @var MockObject|StoreManagerInterface
     */
    private $mockStoreManager;

    /**
     * @var MockObject|OptionsFactory
     */
    private $mockOptionsFactory;

    /**
     * @var MockObject|ConfigHelper
     */
    private $mockConfig;

    /**
     * @var MockObject|ReviewAnswerProcessor
     */
    private $mockReviewAnswerProcessor;

    /**
     * @var MockObject|LoggerInterface
     */
    private $mockLogger;

    /**
     * @var MockObject|Json
     */
    private $mockJsonSerializer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->mockReview = $this->getMockBuilder(Review::class)
                                 ->disableOriginalConstructor()
                                 ->addMethods(['getStoreId', 'getEntityPkValue', 'getDetail', 'getNickname', 'getTitle'])
                                 ->onlyMethods(['getId'])
                                 ->getMock();

        $this->mockVote           = $this->createMock(Vote::class);
        $this->mockVoteCollection = $this->createMock(VoteCollection::class);
        $this->mockReviewFactory  = $this->createMock(ReviewFactory::class);
        $this->mockReviewFactory->method('create')->willReturn($this->mockReview);
        $this->mockReviewResource = $this->createMock(ReviewResource::class);
        $this->mockVoteFactory    = $this->createMock(VoteFactory::class);
        $this->mockVoteFactory->method('create')->willReturn($this->mockVote);
        $this->mockVoteCollectionFactory = $this->createMock(VoteCollectionFactory::class);
        $this->mockVoteCollectionFactory->method('create')->willReturn($this->mockVoteCollection);
        $this->mockStoreManager          = $this->createMock(StoreManagerInterface::class);
        $this->mockOptionsFactory        = $this->createMock(OptionsFactory::class);
        $this->mockConfig                = $this->createMock(ConfigHelper::class);
        $this->mockReviewAnswerProcessor = $this->createMock(ReviewAnswerProcessor::class);
        $this->mockLogger                = $this->createMock(LoggerInterface::class);
        $this->mockJsonSerializer        = $this->createMock(Json::class);
        $this->storeMock                 = $this->createMock(Store::class);
        $this->mockOptions               = $this->createMock(OptionsInterface::class);

        $this->generator = new Generator(
            $this->mockReviewResource,
            $this->mockReviewFactory,
            $this->mockReviewAnswerProcessor,
            $this->mockOptionsFactory,
            $this->mockConfig,
            $this->mockJsonSerializer,
            $this->mockStoreManager,
            $this->mockVoteFactory,
            $this->mockVoteCollectionFactory,
            $this->mockLogger
        );
    }

    /**
     * Test case for the method testGenerateForReviewByIdReturnsExpectedAnswer.
     *
     * @return void
     * @throws NoSuchEntityException If the review with the provided ID does not exist.
     */
    public function testGenerateForReviewByIdReturnsExpectedAnswer(): void
    {
        $testReviewId            = 123;
        $expectedResponseContent = "Dummy generated answer for review";
        $expectedResponse        = $this->createMock(ResponseInterface::class);
        $expectedResponse->method('getContent')->willReturn($expectedResponseContent);

        $this->mockReview->expects($this->atLeastOnce())->method('getId')->willReturn($testReviewId);
        $this->mockReview->expects($this->atLeastOnce())->method('getStoreId')->willReturn(1);
        $this->mockReview->expects($this->atLeastOnce())->method('getEntityPkValue')->willReturn(1);

        $voteItems = [];
        $this->mockVoteCollection->expects($this->once())
                                 ->method('setReviewFilter')
                                 ->willReturnSelf();
        $this->mockVoteCollection->expects($this->once())
                                 ->method('addRatingInfo')
                                 ->willReturnSelf();
        $this->mockVoteCollection->expects($this->once())
                                 ->method('load')
                                 ->willReturn($voteItems);

        $this->mockStoreManager->expects($this->once())
                               ->method('getStore')
                               ->with(1)
                               ->willReturn($this->storeMock);

        $this->storeMock->expects($this->once())
                        ->method('getName')
                        ->willReturn('Test Store');

        $this->mockOptionsFactory->expects($this->once())
                                 ->method('create')
                                 ->willReturn($this->mockOptions);

        $this->mockReviewFactory->expects($this->once())
                                ->method('create')
                                ->willReturn($this->mockReview);

        $this->mockReviewAnswerProcessor->expects($this->once())
                                        ->method('execute')
                                        ->willReturn($expectedResponse);

        $result = $this->generator->generateForReviewById($testReviewId);
        $this->assertEquals($expectedResponseContent, $result);
    }

    /**
     * Test case for the method testGenerateForReviewByIdThrowsExceptionForInvalidId.
     *
     * @return void
     * @throws NoSuchEntityException If the review with the provided ID does not exist.
     */
    public function testGenerateForReviewByIdThrowsExceptionForInvalidId(): void
    {
        $invalidReviewId = 999;

        $this->mockReview->method('getId')->willReturn(null);

        $this->mockReviewFactory->method('create')->willReturn($this->mockReview);

        $this->expectException(NoSuchEntityException::class);
        $this->expectExceptionMessage('Review with ID ' . $invalidReviewId . ' does not exist');

        $this->generator->generateForReviewById($invalidReviewId);
    }

    /**
     * Test case for the method testGetContextReturnsCorrectData.
     *
     * @return void
     * @throws NoSuchEntityException If the review with the provided ID does not exist.
     */
    public function testGetContextReturnsCorrectData(): void
    {
        $reviewId  = 123;
        $storeId   = 1;
        $storeName = 'Test Store';
        $nickname  = 'Nickname';
        $detail    = 'Detail';
        $title     = 'Title';

        // @important The sort order of properties matters!
        $reviewContext                  = [];
        $reviewContext['review_text']   = $detail;
        $reviewContext['customer_name'] = $nickname;
        $reviewContext['review_title']  = $title;

        $reviewContextJson = json_encode($reviewContext);
        // @important The sort order of properties matters!
        $storeContext     = [
            'store_name' => $storeName
        ];
        $storeContextJson = json_encode($storeContext);

        $expectedContext = [
            $reviewContextJson,
            $storeContextJson
        ];

        $serializerMap = [
            [$reviewContext, $reviewContextJson],
            [$storeContext, $storeContextJson]
        ];

        $this->mockJsonSerializer
            ->method('serialize')
            ->will($this->returnValueMap($serializerMap));

        $this->mockReview->method('getId')->willReturn($reviewId);
        $this->mockReview->method('getStoreId')->willReturn($storeId);
        $this->mockReview->method('getDetail')->willReturn($detail);
        $this->mockReview->method('getNickname')->willReturn($nickname);
        $this->mockReview->method('getTitle')->willReturn($title);

        $this->mockStoreManager->method('getStore')->with($storeId)->willReturn($this->storeMock);
        $this->storeMock->method('getName')->willReturn($storeName);

        $this->mockVoteCollectionFactory->method('create')->willReturn($this->mockVoteCollection);
        $this->mockVoteCollection->method('setReviewFilter')->willReturnSelf();
        $this->mockVoteCollection->method('addRatingInfo')->willReturnSelf();
        $this->mockVoteCollection->method('load')->willReturn([]);

        $this->mockOptionsFactory->expects($this->once())
                                 ->method('create')
                                 ->willReturn($this->mockOptions);

        $this->mockConfig->expects($this->once())
                         ->method('getContentFoAnswerOnReview')
                         ->willReturn('Dummy prompt');

        $this->mockReviewAnswerProcessor->expects($this->once())
                                        ->method('execute')
                                        ->with(
                                            $this->stringContains('Dummy prompt'),
                                            $this->equalTo($expectedContext),
                                            $this->mockOptions
                                        )
                                        ->willReturn($this->createMock(ResponseInterface::class));

        $result = $this->generator->generateForReviewById($reviewId);
    }

    /**
     * Test case for the method testGetReviewRatingsAsPercentagesReturnsCorrectData.
     *
     * @return void
     */
    public function testGetReviewRatingsAsPercentagesReturnsCorrectData(): void
    {
        $reviewId = 123;

        $vote1 = $this->getMockBuilder(Vote::class)
                      ->disableOriginalConstructor()
                      ->addMethods(['getPercent', 'getRatingCode'])
                      ->getMock();
        $vote1->method('getPercent')->willReturn(80); // Предполагаемый процент для первого рейтинга
        $vote1->method('getRatingCode')->willReturn('Quality');

        $vote2 = $this->getMockBuilder(Vote::class)
                      ->disableOriginalConstructor()
                      ->addMethods(['getPercent', 'getRatingCode'])
                      ->getMock();
        $vote2->method('getPercent')->willReturn(60); // Предполагаемый процент для второго рейтинга
        $vote2->method('getRatingCode')->willReturn('Value');

        $votes = [$vote1, $vote2];

        $this->mockVoteCollectionFactory->method('create')->willReturn($this->mockVoteCollection);
        $this->mockVoteCollection->method('setReviewFilter')->willReturnSelf();
        $this->mockVoteCollection->method('addRatingInfo')->willReturnSelf();
        $this->mockVoteCollection->method('load')->willReturn($votes);

        $reflection = new \ReflectionClass($this->generator);
        $method = $reflection->getMethod('getReviewRatingsAsPercentages');
        $method->setAccessible(true);
        $result = $method->invokeArgs($this->generator, [$reviewId]);

        $expected = [
            'Quality' => 80,
            'Value' => 60
        ];

        $this->assertEquals($expected, $result);
    }

    /**
     * Test case for the method testGenerateAnswerReturnsCorrectResponse.
     *
     * @return void
     * @throws \ReflectionException If the method 'generateAnswer' cannot be accessed using reflection.
     */
    public function testGenerateAnswerReturnsCorrectResponse(): void
    {
        $content = 'Sample content for review';
        $context = ['Sample context'];
        $expectedResponseContent = 'Generated answer for content';

        $expectedResponse = $this->createMock(ResponseInterface::class);
        $expectedResponse->method('getContent')->willReturn($expectedResponseContent);
        $expectedResponse->method('getChatResponse')->willReturn(['error' => null]);

        $this->mockReviewAnswerProcessor->expects($this->once())
                                        ->method('execute')
                                        ->with($content, $context, $this->mockOptions)
                                        ->willReturn($expectedResponse);

        $reflection = new \ReflectionClass($this->generator);
        $method = $reflection->getMethod('generateAnswer');
        $method->setAccessible(true);
        $result = $method->invokeArgs($this->generator, [$content, $context, $this->mockOptions]);

        $this->assertEquals($expectedResponseContent, $result);
    }
}
