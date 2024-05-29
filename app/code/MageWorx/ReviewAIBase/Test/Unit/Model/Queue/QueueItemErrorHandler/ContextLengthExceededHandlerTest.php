<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types = 1);

namespace Unit\Model\Queue\QueueItemErrorHandler;

use MageWorx\OpenAI\Api\Data\QueueItemInterface;
use MageWorx\OpenAI\Api\Data\QueueProcessInterface;
use MageWorx\OpenAI\Api\GeneralOpenAIHelperInterface;
use MageWorx\OpenAI\Api\OptionsInterface;
use MageWorx\OpenAI\Api\QueueManagementInterface;
use MageWorx\OpenAI\Api\QueueProcessManagementInterface;
use MageWorx\OpenAI\Api\QueueRepositoryInterface;
use MageWorx\OpenAI\Api\ResponseInterface;
use MageWorx\OpenAI\Model\ResourceModel\QueueItem as QueueItemResource;
use MageWorx\ReviewAIBase\Helper\Config as ConfigHelper;
use MageWorx\ReviewAIBase\Model\Queue\QueueItemErrorHandler\ContextLengthExceededHandler;
use PHPUnit\Framework\TestCase;

class ContextLengthExceededHandlerTest extends TestCase
{
    /**
     * Test to ensure that the error handler correctly handles the 'context length exceeded' error.
     * This test simulates a scenario where a queue item triggers an error due to the context length exceeding
     * the maximum allowed limit. The test verifies that the handler correctly processes this specific error
     * by splitting the request into smaller parts, creating new queue items for each part, and then
     * updating the status of the original queue item to prevent further processing.
     *
     * The test checks the following:
     * 1. The queue item receives the correct error response.
     * 2. The queue item is updated to the 'processing' status initially to prevent further processing.
     * 3. The original content and context are split into smaller parts, and new queue items are created for each part.
     * 4. A summary queue item is created and dependent on the new queue items.
     * 5. The original queue item's status is updated to 'completed' after handling the error.
     *
     * The test ensures that all necessary methods are called with the expected parameters and that the
     * handler returns 'true', indicating the error has been handled.
     */
    public function testContextLengthExceededErrorIsHandledCorrectly(): void
    {
        $storeId          = 1;
        $additionalData   = ['product_id' => 1, 'store_id' => $storeId];
        $maxContextLength = 512;
        $summarizeMessage = 'Summarize this reviews';
        $mainQueueItemId  = 1;

        // Options mock
        $options = $this->getMockForAbstractClass(OptionsInterface::class);
        $options->expects($this->once())->method('getModel')->willReturn('gpt4');

        // Queue items mock
        $summaryQueueItem = $this->createMock(QueueItemInterface::class);
        $queueItem2       = $this->createMock(QueueItemInterface::class);
        $queueItem        = $this->createMock(QueueItemInterface::class);
        $queueItem->method('getId')->willReturn($mainQueueItemId);
        $queueItem->method('getResponse')->willReturn(
            $this->createResponseMock(
                true, [
                        'error' => ['type' => 'invalid_request_error', 'code' => 'context_length_exceeded']
                    ]
            )
        );
        $queueItem->method('getContent')->willReturn('content');
        $queueItem->method('getContext')->willReturn(['context']);
        $queueItem->method('getAdditionalData')->willReturn($additionalData);
        $queueItem->expects($this->atLeastOnce())->method('getOptions')->willReturn($options);

        // General helper mock
        $generalHelper = $this->createMock(GeneralOpenAIHelperInterface::class);
        $generalHelper->expects($this->once())->method('getMaxModelSupportedContextLength')->willReturn($maxContextLength);
        $generalHelper->expects($this->once())->method('splitRequestToParts')->with('content', ['context'], $maxContextLength)
                      ->willReturn([['context']]);

        // Process mock
        $queueProcessManagement = $this->createMock(QueueProcessManagementInterface::class);
        $processMock            = $this->getMockForAbstractClass(QueueProcessInterface::class);
        $queueProcessManagement->method('registerProcess')->willReturn($processMock);

        // Queue repository mock
        $queueRepository = $this->createMock(QueueRepositoryInterface::class);

        // Config mock
        $config = $this->createMock(ConfigHelper::class);
        $config->expects($this->once())->method('getSummarizeMessage')->with($storeId)
               ->willReturn($summarizeMessage);

        // Queue management mock
        $queueManagement = $this->createMock(QueueManagementInterface::class);
        $queueManagement->expects($this->exactly(2))
                        ->method('addToQueue')
                        ->withConsecutive(
                            ['content', $options, null, ['context'], $processMock, $additionalData],
                            [$summarizeMessage, $options, 'review_ai_summary', [], $processMock, $additionalData, 'review_ai_summary_from_summary', 'review_ai_summary']
                        )
                        ->willReturnOnConsecutiveCalls(
                            $queueItem2,
                            $summaryQueueItem
                        );
        $queueManagement->expects($this->once())->method('addDependency')
                        ->with($summaryQueueItem, [$queueItem2]);

        // Queue item resource mock
        $queueItemResource = $this->createMock(QueueItemResource::class);
        $queueItemResource->expects($this->exactly(2))
                          ->method('updateStatus')
                          ->withConsecutive(
                              [$this->equalTo($mainQueueItemId), $this->equalTo(QueueItemInterface::STATUS_PROCESSING)],
                              [$this->equalTo($mainQueueItemId), $this->equalTo(QueueItemInterface::STATUS_COMPLETED)]
                          );

        $handler = new ContextLengthExceededHandler(
            $generalHelper,
            $queueManagement,
            $queueProcessManagement,
            $queueRepository,
            $queueItemResource,
            $config
        );

        $result = $handler->execute($queueItem);
        $this->assertTrue($result);
    }

    /**
     * Test to ensure that the error handler does not handle errors
     * when the conditions for 'context length exceeded' are not met.
     */
    public function testContextLengthExceededErrorIsNotHandledWhenNotApplicable(): void
    {
        // Create a mock of QueueItemInterface
        $queueItem = $this->createMock(QueueItemInterface::class);

        // Configure the mock to return an error response that does not match
        // the 'context length exceeded' error type and code
        $queueItem->method('getResponse')->willReturn(
            $this->createResponseMock(
                true, // Indicates that there is an error
                [
                    'error' => [
                        'type' => 'some_other_error_type', // A different error type
                        'code' => 'some_other_error_code'  // A different error code
                    ]
                ]
            )
        );

        $generalHelper          = $this->createMock(GeneralOpenAIHelperInterface::class);
        $queueManagement        = $this->createMock(QueueManagementInterface::class);
        $queueProcessManagement = $this->createMock(QueueProcessManagementInterface::class);
        $queueRepository        = $this->createMock(QueueRepositoryInterface::class);
        $queueItemResource      = $this->createMock(QueueItemResource::class);
        $config                 = $this->createMock(ConfigHelper::class);

        $handler = new ContextLengthExceededHandler(
            $generalHelper,
            $queueManagement,
            $queueProcessManagement,
            $queueRepository,
            $queueItemResource,
            $config
        );

        // Execute the `execute` method and assert that it returns false
        // as the conditions for handling the error are not met
        $result = $handler->execute($queueItem);

        $this->assertFalse($result);
    }

    /**
     * Test to ensure that the error handler does not handle cases
     * where the 'error' key is missing in the response content.
     * This simulates a scenario where the response does not contain an error,
     * and thus should not be handled by the ContextLengthExceededHandler.
     */
    public function testErrorNotHandledWhenErrorKeyIsMissing(): void
    {
        // Create a mock of QueueItemInterface
        $queueItem = $this->createMock(QueueItemInterface::class);

        // Configure the mock to return a response that does not contain the 'error' key
        $queueItem->method('getResponse')->willReturn(
            $this->createResponseMock(
                true, // Indicates that there is a response, but not necessarily an error
                ['not_error_key' => 'some_value'] // Response content without 'error' key
            )
        );

        // Mock other dependencies
        $generalHelper          = $this->createMock(GeneralOpenAIHelperInterface::class);
        $queueManagement        = $this->createMock(QueueManagementInterface::class);
        $queueProcessManagement = $this->createMock(QueueProcessManagementInterface::class);
        $queueRepository        = $this->createMock(QueueRepositoryInterface::class);
        $queueItemResource      = $this->createMock(QueueItemResource::class);
        $config                 = $this->createMock(ConfigHelper::class);

        // Initialize the error handler
        $handler = new ContextLengthExceededHandler(
            $generalHelper,
            $queueManagement,
            $queueProcessManagement,
            $queueRepository,
            $queueItemResource,
            $config
        );

        // Execute the `execute` method and assert that it returns false
        // as the 'error' key is not present in the response content
        $result = $handler->execute($queueItem);

        // Assert that the handler does not process this case
        $this->assertFalse($result);
    }

    /**
     * Test to ensure that the error handler does not handle cases
     * where the response indicates that there is no error.
     * This simulates a scenario where the response is successful (no error),
     * and thus should not be handled by the ContextLengthExceededHandler.
     */
    public function testErrorNotHandledWhenResponseIndicatesNoError(): void
    {
        // Create a mock of QueueItemInterface
        $queueItem = $this->createMock(QueueItemInterface::class);

        // Configure the mock to return a response indicating no error
        $queueItem->method('getResponse')->willReturn(
            $this->createResponseMock(
                false, // Indicates that there is no error in the response
                [] // Any response content
            )
        );

        // Mock other dependencies
        $generalHelper          = $this->createMock(GeneralOpenAIHelperInterface::class);
        $queueManagement        = $this->createMock(QueueManagementInterface::class);
        $queueProcessManagement = $this->createMock(QueueProcessManagementInterface::class);
        $queueRepository        = $this->createMock(QueueRepositoryInterface::class);
        $queueItemResource      = $this->createMock(QueueItemResource::class);
        $config                 = $this->createMock(ConfigHelper::class);

        // Initialize the error handler
        $handler = new ContextLengthExceededHandler(
            $generalHelper,
            $queueManagement,
            $queueProcessManagement,
            $queueRepository,
            $queueItemResource,
            $config
        );

        // Execute the `execute` method and assert that it returns false
        // as the response indicates that there is no error
        $result = $handler->execute($queueItem);

        // Assert that the handler does not process this case
        $this->assertFalse($result);
    }

    /**
     * Helper method to create a mock of ResponseInterface
     * that simulates an error response.
     *
     * @param bool $isError Indicates if the response is an error
     * @param array $content The content of the error response
     * @return ResponseInterface
     */
    private function createResponseMock(bool $isError, array $content): ResponseInterface
    {
        $responseMock = $this->createMock(ResponseInterface::class);
        $responseMock->method('getIsError')->willReturn($isError);
        $responseMock->method('getChatResponse')->willReturn($content);

        return $responseMock;
    }
}
