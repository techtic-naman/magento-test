<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types = 1);

namespace MageWorx\ReviewAIBase\Test\Unit\Model;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Review\Model\Rating\Option\Vote;
use Magento\Review\Model\ResourceModel\Review\Collection as ReviewCollection;
use Magento\Review\Model\ResourceModel\Review\CollectionFactory as ReviewCollectionFactory;
use Magento\Review\Model\Review;
use MageWorx\OpenAI\Api\GeneralOpenAIHelperInterface;
use MageWorx\OpenAI\Api\OptionsInterfaceFactory;
use MageWorx\OpenAI\Model\Models\ChatGPT4Model;
use MageWorx\OpenAI\Model\Models\ModelsFactory;
use MageWorx\OpenAI\Model\Options;
use MageWorx\OpenAI\Model\OptionsFactory;
use MageWorx\OpenAI\Api\OptionsInterface;
use MageWorx\OpenAI\Api\ResponseInterface;
use MageWorx\OpenAI\Model\Response;
use MageWorx\ReviewAIBase\Model\ReviewSummaryGenerator;
use MageWorx\ReviewAIBase\Model\ReviewSummaryProcessor;
use Magento\Store\Model\StoreManagerInterface;
use MageWorx\ReviewAIBase\Model\ReviewSummarySaver;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

/**
 * Tests the functionality of the ReviewSummaryGenerator model.
 *
 * This class contains unit tests for ReviewSummaryGenerator, which
 * is responsible for generating a summary of product reviews. It covers
 * different scenarios including successful summary generation, error handling,
 * and expected behavior when the API returns errors or null responses.
 *
 * The tests ensure that:
 * - A correct summary is generated under normal circumstances.
 * - Proper error handling is in place when the API or processing fails.
 * - The system logs the appropriate information for debugging purposes.
 * - An empty string is returned when the API provides an error response or null,
 *   to prevent any disruption on the client-side.
 */
class ReviewSummaryGeneratorTest extends TestCase
{
    private $objectManager;
    private $reviewCollectionFactoryMock;
    private $optionsFactoryMock;
    private $storeManagerMock;
    private $loggerMock;
    private $reviewSummaryProcessorMock;
    private $responseMock;
    /**
     * @var object|ReviewSummaryGenerator
     */
    private $reviewSummaryGenerator;

    protected function setUp(): void
    {
        $this->objectManager = new ObjectManager($this);

        // Mock dependencies
        $this->reviewCollectionFactoryMock = $this->createMock(ReviewCollectionFactory::class);
        $this->optionsMock                 = $this->createMock(Options::class);
        $this->optionsFactoryMock          = $this->getMockBuilder(OptionsInterfaceFactory::class)
                                                  ->disableOriginalConstructor()
                                                  ->onlyMethods(['create'])
                                                  ->getMockForAbstractClass();
        $this->optionsFactoryMock->method('create')
                                 ->willReturn($this->optionsMock);

        $this->modelMock         = $this->createMock(ChatGPT4Model::class);
        $this->modelsFactoryMock = $this->getMockBuilder(ModelsFactory::class)
                                        ->disableOriginalConstructor()
                                        ->setMethods(['create'])
                                        ->getMockForAbstractClass();
        $this->modelsFactoryMock->method('create')
                                ->willReturn($this->modelMock);

        $this->storeManagerMock           = $this->createMock(StoreManagerInterface::class);
        $this->loggerMock                 = $this->createMock(LoggerInterface::class);
        $this->reviewSummaryProcessorMock = $this->createMock(ReviewSummaryProcessor::class);
        $this->reviewSummarySaverMock     = $this->createMock(ReviewSummarySaver::class);
        $this->responseMock               = $this->createMock(Response::class);
        $this->productRepositoryMock      = $this->getMockBuilder(ProductRepositoryInterface::class)
                                                 ->disableOriginalConstructor()
                                                 ->getMockForAbstractClass();
        $this->configHelperMock           = $this->getMockBuilder(\MageWorx\ReviewAIBase\Helper\Config::class)
                                                 ->disableOriginalConstructor()
                                                 ->getMock();

        $this->generalOpenAIHelper = $this->getMockBuilder(GeneralOpenAIHelperInterface::class)
                                          ->disableOriginalConstructor()
                                          ->getMock();

        // Create the instance of ReviewSummaryGenerator with mocked dependencies
        $this->reviewSummaryGenerator = $this->objectManager->getObject(
            ReviewSummaryGenerator::class,
            [
                'reviewSummaryProcessor'  => $this->reviewSummaryProcessorMock,
                'reviewCollectionFactory' => $this->reviewCollectionFactoryMock,
                'reviewSummarySaver'      => $this->reviewSummarySaverMock,
                'modelsFactory'           => $this->modelsFactoryMock,
                'productRepository'       => $this->productRepositoryMock,
                'optionsFactory'          => $this->optionsFactoryMock,
                'generalHelper'           => $this->generalOpenAIHelper,
                'storeManager'            => $this->storeManagerMock,
                'config'                  => $this->configHelperMock,
                'logger'                  => $this->loggerMock,
            ]
        );
    }

    /**
     * Test the behavior of the `generate` method for a successful scenario.
     *
     * This test focuses on the successful generation of the review summary.
     *
     * Steps:
     * 1. Set up mock behaviors for the review collection and its methods.
     * 2. Mock two individual reviews and their rating votes.
     * 3. Mock the options used for processing the review summary.
     * 4. Mock the ReviewSummaryProcessor's behavior to return dummy content.
     * 5. Finally, asserts that the return value of `generate` matches the expected content.
     *
     * @return void
     * @throws LocalizedException
     */
    public function testGenerate(): void
    {
        // Mock reviewCollection
        $reviewCollection = $this->createMock(ReviewCollection::class);
        $this->reviewCollectionFactoryMock->expects($this->once())
                                          ->method('create')
                                          ->willReturn($reviewCollection);

        $reviewCollection->expects($this->once())
                         ->method('addStoreFilter')
                         ->willReturnSelf();

        $reviewCollection->expects($this->once())
                         ->method('addStatusFilter')
                         ->willReturnSelf();

        $reviewCollection->expects($this->once())
                         ->method('addEntityFilter')
                         ->willReturnSelf();

        $reviewCollection->expects($this->once())
                         ->method('setDateOrder')
                         ->willReturnSelf();

        $reviewCollection->expects($this->once())
                         ->method('addRateVotes')
                         ->willReturnSelf();

        // Mock review returned from collection mock
        $reviewModelMagicMethods = ['getTitle', 'getRatingVotes', 'getDetail', 'getCreatedAt'];
        $voteModelMagicMethods   = ['getRatingCode', 'getPercent'];

        $review1 = $this->getMockBuilder(Review::class)
                        ->disableOriginalConstructor()
                        ->setMethods($reviewModelMagicMethods)
                        ->getMock();
        $review2 = $this->getMockBuilder(Review::class)
                        ->disableOriginalConstructor()
                        ->setMethods($reviewModelMagicMethods)
                        ->getMock();

        // Mock the getTitle, getRatingVotes, getDetail, and getCreatedAt methods for $review1
        $review1->expects($this->any())
                ->method('getTitle')
                ->willReturn('Review Title 1');

        $vote1 = $this->getMockBuilder(Vote::class)
                      ->disableOriginalConstructor()
                      ->setMethods($voteModelMagicMethods)
                      ->getMock();
        $vote1->expects($this->any())
              ->method('getRatingCode')
              ->willReturn('Quality');
        $vote1->expects($this->any())
              ->method('getPercent')
              ->willReturn(90);

        $vote2 = $this->getMockBuilder(Vote::class)
                      ->disableOriginalConstructor()
                      ->setMethods($voteModelMagicMethods)
                      ->getMock();
        $vote2->expects($this->any())
              ->method('getRatingCode')
              ->willReturn('Price');
        $vote2->expects($this->any())
              ->method('getPercent')
              ->willReturn(80);

        $review1->expects($this->any())
                ->method('getRatingVotes')
                ->willReturn([$vote1, $vote2]);

        $review1->expects($this->any())
                ->method('getDetail')
                ->willReturn('This is review details for product 1.');

        $review1->expects($this->any())
                ->method('getCreatedAt')
                ->willReturn('2023-10-28 10:00:00');

        // Mock the getTitle, getRatingVotes, getDetail, and getCreatedAt methods for $review2
        $review2->expects($this->any())
                ->method('getTitle')
                ->willReturn('Review Title 2');

        $review2->expects($this->any())
                ->method('getRatingVotes')
                ->willReturn([]);

        $review2->expects($this->any())
                ->method('getDetail')
                ->willReturn('This is review details for product 2.');

        $review2->expects($this->any())
                ->method('getCreatedAt')
                ->willReturn('2023-10-29 12:00:00');

        $reviewCollection->expects($this->any())
                         ->method('getIterator')
                         ->willReturn(new \ArrayIterator([$review1, $review2]));

        // Mock OptionsInterface
        $this->optionsMock->expects($this->atLeastOnce())
                          ->method('getModel')
                          ->willReturn('gpt-4');

        $this->optionsFactoryMock->expects($this->once())
                                 ->method('create')
                                 ->willReturn($this->optionsMock);

        $this->generalOpenAIHelper->expects($this->once())
                                  ->method('getMaxModelSupportedContextLength')
                                  ->with('gpt-4')
                                  ->willReturn(4096);

        // Mock ReviewSummaryProcessor
        $content  = 'Dummy content returned by dummy api call';
        $response = $this->createMock(ResponseInterface::class);
        $response->expects($this->atLeastOnce())
                 ->method('getContent')
                 ->willReturn($content);

        $this->reviewSummaryProcessorMock->expects($this->once())
                                         ->method('execute')
                                         ->willReturn($response);

        $productId = 123;
        $storeId   = 1;

        $this->reviewSummarySaverMock->expects($this->once())
                                     ->method('saveUpdate')
                                     ->with($content, $productId, $storeId);

        $result = $this->reviewSummaryGenerator->generate($productId, $storeId);

        $this->assertEquals($content, $result);
    }

    /**
     * Test the behavior of the `generate` method for an error scenario.
     *
     * This test aims to simulate an error scenario where the `ReviewSummaryProcessor`
     * throws an exception during the generation of the review summary.
     *
     * Steps:
     * 1. Set up mock behaviors for the review collection and its methods.
     * 2. Mock the options used for processing the review summary.
     * 3. Mock the ReviewSummaryProcessor's behavior to throw an exception.
     * 4. Expect the test to capture the exception and log an error.
     * 5. Verify that the `saveUpdate` method of the `reviewSummarySaverMock` is never called.
     *
     * @return void
     */
    public function testGenerateError(): void
    {
        // Mock reviewCollection
        $reviewCollection = $this->createMock(ReviewCollection::class);
        $this->reviewCollectionFactoryMock->expects($this->once())
                                          ->method('create')
                                          ->willReturn($reviewCollection);

        $reviewCollection->expects($this->once())
                         ->method('addStoreFilter')
                         ->willReturnSelf();

        $reviewCollection->expects($this->once())
                         ->method('addStatusFilter')
                         ->willReturnSelf();

        $reviewCollection->expects($this->once())
                         ->method('addEntityFilter')
                         ->willReturnSelf();

        $reviewCollection->expects($this->once())
                         ->method('setDateOrder')
                         ->willReturnSelf();

        $reviewCollection->expects($this->once())
                         ->method('addRateVotes')
                         ->willReturnSelf();

        // Mock review returned from collection mock
        $reviewCollection->expects($this->any())
                         ->method('getIterator')
                         ->willReturn(new \ArrayIterator([]));

        // Mock OptionsInterface
        $this->optionsMock->expects($this->atLeastOnce())
                          ->method('getModel')
                          ->willReturn('gpt-4');

        $this->optionsFactoryMock->expects($this->once())
                                 ->method('create')
                                 ->willReturn($this->optionsMock);

        $this->generalOpenAIHelper->expects($this->once())
                                  ->method('getMaxModelSupportedContextLength')
                                  ->with('gpt-4')
                                  ->willReturn(4096);

        // Mock ReviewSummaryProcessor
        $this->reviewSummaryProcessorMock->expects($this->atLeastOnce())
                                         ->method('execute')
                                         ->with(
                                             $this->isType('string'), // Expecting a string as the first argument
                                             $this->equalTo([]),      // Expecting an empty array as the second argument
                                             $this->isInstanceOf(OptionsInterface::class) // Expecting the exact same object as $options as the third argument
                                         )
                                         ->willThrowException(new LocalizedException(__('Test exception message')));

        $this->expectException(LocalizedException::class);

        $this->loggerMock->expects($this->atLeastOnce())
                         ->method('error');

        $productId = 123;
        $storeId   = 1;

        $this->reviewSummarySaverMock->expects($this->never())
                                     ->method('saveUpdate')
                                     ->with($this->isType('string'), $productId, $storeId);

        $result = $this->reviewSummaryGenerator->generate($productId, $storeId);
    }

    /**
     * Test the behavior of the `generate` method.
     *
     * This test focuses on the scenario where the method `sendSummaryDataToAPI`
     * returns a null value, and the expected behavior should be logging a warning
     * and returning an empty string.
     *
     * Steps:
     * 1. Mocks the behavior of `execute` method to return a response mock.
     * 2. Ensures the `getContent` method of the response is never called.
     * 3. Simulates a chat response with an error.
     * 4. Sets up a mock review collection with necessary method expectations.
     * 5. Checks if a warning is logged with the correct parameters.
     * 6. Finally, asserts that the return value of `generate` is an empty string.
     *
     * @return void
     * @throws LocalizedException
     */
    public function testReturnsEmptyStringWhenSendSummaryDataToAPIReturnsNull()
    {
        $productId             = 123;
        $storeId               = 2;
        $chatResponseWithError = [
            'error' => 'Empty response'
        ];

        $this->reviewSummaryProcessorMock->method('execute')
                                         ->willReturn($this->responseMock);

        $this->responseMock->expects($this->never())
                           ->method('getContent');

        $this->responseMock->expects($this->atLeastOnce())
                           ->method('getChatResponse')
                           ->willReturn($chatResponseWithError);

        $this->optionsMock->expects($this->atLeastOnce())
                          ->method('getModel')
                          ->willReturn('gpt-4');

        $this->optionsFactoryMock->expects($this->once())
                                 ->method('create')
                                 ->willReturn($this->optionsMock);

        $this->generalOpenAIHelper->expects($this->once())
                                  ->method('getMaxModelSupportedContextLength')
                                  ->with('gpt-4')
                                  ->willReturn(4096);

        // Mock reviewCollection
        $reviewCollection = $this->createMock(ReviewCollection::class);
        $this->reviewCollectionFactoryMock->expects($this->once())
                                          ->method('create')
                                          ->willReturn($reviewCollection);

        $reviewCollection->expects($this->once())
                         ->method('addStoreFilter')
                         ->willReturnSelf();

        $reviewCollection->expects($this->once())
                         ->method('addStatusFilter')
                         ->willReturnSelf();

        $reviewCollection->expects($this->once())
                         ->method('addEntityFilter')
                         ->willReturnSelf();

        $reviewCollection->expects($this->once())
                         ->method('setDateOrder')
                         ->willReturnSelf();

        $reviewCollection->expects($this->once())
                         ->method('addRateVotes')
                         ->willReturnSelf();

        $reviewCollection->expects($this->any())
                         ->method('getIterator')
                         ->willReturn(new \ArrayIterator([]));

        $this->loggerMock->expects($this->once())
                         ->method('warning')
                         ->with('Empty response', []);

        $result = $this->reviewSummaryGenerator->generate($productId, $storeId);

        $this->assertEquals('', $result, 'In case of error in Chat Response return value must be an empty string.');
    }

    /**
     * Test the behavior of the `generate` method when the chat response contains errors.
     *
     * This test validates that if the API's chat response includes an 'error' key with an array of errors,
     * those errors are correctly logged in JSON format, and no other exceptions are thrown.
     * The method should also return an empty string, indicating a graceful handling of the error scenario.
     *
     * Steps:
     * 1. Mock the `ReviewSummaryProcessor` to return a pre-defined response with errors.
     * 2. Ensure that the `ResponseInterface`'s `getContent` method is never called.
     * 3. Configure the `ResponseInterface` to return the errors array upon calling `getChatResponse`.
     * 4. Mock all interactions with the `ReviewCollection` to simulate a standard review collection flow.
     * 5. Assert that the `LoggerInterface` captures the warning with the correct error details in JSON format.
     * 6. Verify that the `generate` method returns an empty string as expected.
     *
     * @return void
     * @throws LocalizedException
     */
    public function testGenerateHandlesChatResponseError(): void
    {
        $productId             = 123;
        $storeId               = 1;
        $errors                = ['error1' => 'Error message 1', 'error2' => 'Error message 2'];
        $chatResponseWithError = [
            'error' => $errors
        ];

        $this->reviewSummaryProcessorMock->method('execute')
                                         ->willReturn($this->responseMock);

        $this->responseMock->expects($this->never())
                           ->method('getContent');

        $this->responseMock->expects($this->atLeastOnce())
                           ->method('getChatResponse')
                           ->willReturn($chatResponseWithError);

        $this->optionsMock->expects($this->atLeastOnce())
                          ->method('getModel')
                          ->willReturn('gpt-4');

        $this->optionsFactoryMock->expects($this->once())
                                 ->method('create')
                                 ->willReturn($this->optionsMock);

        $this->generalOpenAIHelper->expects($this->once())
                                  ->method('getMaxModelSupportedContextLength')
                                  ->with('gpt-4')
                                  ->willReturn(4096);

        // Mock reviewCollection
        $reviewCollection = $this->createMock(ReviewCollection::class);
        $this->reviewCollectionFactoryMock->expects($this->once())
                                          ->method('create')
                                          ->willReturn($reviewCollection);

        $reviewCollection->expects($this->once())
                         ->method('addStoreFilter')
                         ->willReturnSelf();

        $reviewCollection->expects($this->once())
                         ->method('addStatusFilter')
                         ->willReturnSelf();

        $reviewCollection->expects($this->once())
                         ->method('addEntityFilter')
                         ->willReturnSelf();

        $reviewCollection->expects($this->once())
                         ->method('setDateOrder')
                         ->willReturnSelf();

        $reviewCollection->expects($this->once())
                         ->method('addRateVotes')
                         ->willReturnSelf();

        $reviewCollection->expects($this->any())
                         ->method('getIterator')
                         ->willReturn(new \ArrayIterator([]));

        $this->loggerMock->expects($this->once())
                         ->method('warning')
                         ->with(
                             $this->callback(
                                 function ($subject) {
                                     $decoded = json_decode($subject, true);
                                     return json_last_error() === JSON_ERROR_NONE && isset($decoded['error1']) && isset($decoded['error2']);
                                 }
                             ),
                             $this->equalTo([])
                         );

        $result = $this->reviewSummaryGenerator->generate($productId, $storeId);

        $this->assertEmpty($result);
    }

    public function testAddProductNameToRequestOnGenerate(): void
    {
        // Input data
        $productId   = 123;
        $productName = 'Custom Product Name!';
        $storeId     = 1;

        $content                = 'Dummy content returned by dummy api call.';
        $contentWithProductName = $content . ' Product: ' . $productName;

        // Mock product
        $productMock = $this->getMockBuilder(\Magento\Catalog\Model\Product::class)
                            ->disableOriginalConstructor()
                            ->onlyMethods(['getName'])
                            ->getMock();

        $this->productRepositoryMock->expects($this->once())
                                    ->method('getById')
                                    ->willReturn($productMock);
        $productMock->expects($this->once())
                    ->method('getName')
                    ->willReturn($productName);

        // Mock config
        $this->configHelperMock->expects($this->once())
                               ->method('getContent')
                               ->with($storeId)
                               ->willReturn($content);

        $this->configHelperMock->expects($this->once())
                               ->method('addProductNameToRequest')
                               ->willReturn(true);

        // Mock reviewCollection
        $reviewCollection = $this->createMock(ReviewCollection::class);
        $this->reviewCollectionFactoryMock->expects($this->once())
                                          ->method('create')
                                          ->willReturn($reviewCollection);

        $reviewCollection->expects($this->once())
                         ->method('addStoreFilter')
                         ->willReturnSelf();

        $reviewCollection->expects($this->once())
                         ->method('addStatusFilter')
                         ->willReturnSelf();

        $reviewCollection->expects($this->once())
                         ->method('addEntityFilter')
                         ->willReturnSelf();

        $reviewCollection->expects($this->once())
                         ->method('setDateOrder')
                         ->willReturnSelf();

        $reviewCollection->expects($this->once())
                         ->method('addRateVotes')
                         ->willReturnSelf();

        // Mock review returned from collection mock
        $reviewModelMagicMethods = ['getTitle', 'getRatingVotes', 'getDetail', 'getCreatedAt'];
        $voteModelMagicMethods   = ['getRatingCode', 'getPercent'];

        $review1 = $this->getMockBuilder(Review::class)
                        ->disableOriginalConstructor()
                        ->setMethods($reviewModelMagicMethods)
                        ->getMock();
        $review2 = $this->getMockBuilder(Review::class)
                        ->disableOriginalConstructor()
                        ->setMethods($reviewModelMagicMethods)
                        ->getMock();

        // Mock the getTitle, getRatingVotes, getDetail, and getCreatedAt methods for $review1
        $review1->expects($this->any())
                ->method('getTitle')
                ->willReturn('Review Title 1');

        $vote1 = $this->getMockBuilder(Vote::class)
                      ->disableOriginalConstructor()
                      ->setMethods($voteModelMagicMethods)
                      ->getMock();
        $vote1->expects($this->any())
              ->method('getRatingCode')
              ->willReturn('Quality');
        $vote1->expects($this->any())
              ->method('getPercent')
              ->willReturn(90);

        $vote2 = $this->getMockBuilder(Vote::class)
                      ->disableOriginalConstructor()
                      ->setMethods($voteModelMagicMethods)
                      ->getMock();
        $vote2->expects($this->any())
              ->method('getRatingCode')
              ->willReturn('Price');
        $vote2->expects($this->any())
              ->method('getPercent')
              ->willReturn(80);

        $review1->expects($this->any())
                ->method('getRatingVotes')
                ->willReturn([$vote1, $vote2]);

        $review1->expects($this->any())
                ->method('getDetail')
                ->willReturn('This is review details for product 1.');

        $review1->expects($this->any())
                ->method('getCreatedAt')
                ->willReturn('2023-10-28 10:00:00');

        // Mock the getTitle, getRatingVotes, getDetail, and getCreatedAt methods for $review2
        $review2->expects($this->any())
                ->method('getTitle')
                ->willReturn('Review Title 2');

        $review2->expects($this->any())
                ->method('getRatingVotes')
                ->willReturn([]);

        $review2->expects($this->any())
                ->method('getDetail')
                ->willReturn('This is review details for product 2.');

        $review2->expects($this->any())
                ->method('getCreatedAt')
                ->willReturn('2023-10-29 12:00:00');

        $reviewCollection->expects($this->any())
                         ->method('getIterator')
                         ->willReturn(new \ArrayIterator([$review1, $review2]));

        // Mock OptionsInterface
        $this->optionsMock->expects($this->atLeastOnce())
                          ->method('getModel')
                          ->willReturn('gpt-4');

        $this->optionsFactoryMock->expects($this->once())
                                 ->method('create')
                                 ->willReturn($this->optionsMock);

        $this->generalOpenAIHelper->expects($this->once())
                                  ->method('getMaxModelSupportedContextLength')
                                  ->with('gpt-4')
                                  ->willReturn(4096);

        // Mock ReviewSummaryProcessor
        $response = $this->createMock(ResponseInterface::class);
        $response->expects($this->atLeastOnce())
                 ->method('getContent')
                 ->willReturn($content);

        $this->reviewSummaryProcessorMock->expects($this->once())
                                         ->method('execute')
                                         ->with($contentWithProductName, $this->isType('array'), $this->isInstanceOf(OptionsInterface::class))
                                         ->willReturn($response);

        $this->reviewSummarySaverMock->expects($this->once())
                                     ->method('saveUpdate')
                                     ->with($content, $productId, $storeId);

        $result = $this->reviewSummaryGenerator->generate($productId, $storeId);

        $this->assertEquals($content, $result);
    }
}
