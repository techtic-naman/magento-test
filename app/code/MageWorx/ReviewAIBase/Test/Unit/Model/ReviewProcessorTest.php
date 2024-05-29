<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types = 1);

namespace MageWorx\ReviewAIBase\Test\Unit\Model;

use Magento\Framework\Exception\LocalizedException;
use MageWorx\OpenAI\Api\RequestInterface;
use MageWorx\OpenAI\Api\ResponseInterface;
use MageWorx\OpenAI\Api\RequestInterfaceFactory;
use MageWorx\OpenAI\Api\ResponseInterfaceFactory;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use MageWorx\OpenAI\Model\Models\ChatGPT3d5Model;
use MageWorx\OpenAI\Model\Models\ModelsFactory;
use MageWorx\ReviewAIBase\Exception\InputDataException;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use MageWorx\ReviewAIBase\Model\ReviewSummaryProcessor;

class ReviewProcessorTest extends TestCase
{
    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * @var (ResponseInterfaceFactory&MockObject)|MockObject
     */
    protected $openAIResponseFactoryMock;
    protected $openAIRequestMock;
    protected $openAIRequestFactoryMock;

    /**
     * @var (ResponseInterface&MockObject)|MockObject
     */
    protected $openAIResponseMock;

    /**
     * @var ReviewSummaryProcessor|object
     */
    protected $reviewPorcessor;

    /**
     * @var (ModelsFactory&MockObject)|MockObject
     */
    protected $openAIModelsFactoryMock;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        $this->objectManager = new ObjectManager($this);

        $this->openAIRequestFactoryMock = $this->getMockBuilder(RequestInterfaceFactory::class)
                                               ->disableOriginalConstructor()
                                               ->setMethods(['create'])
                                               ->getMockForAbstractClass();

        $this->openAIRequestMock = $this->getMockBuilder(RequestInterface::class)
                                        ->disableOriginalConstructor()
                                        ->getMockForAbstractClass();

        $this->openAIResponseFactoryMock = $this->getMockBuilder(ResponseInterfaceFactory::class)
                                                ->disableOriginalConstructor()
                                                ->setMethods(['create'])
                                                ->getMockForAbstractClass();

        $this->openAIResponseMock = $this->getMockBuilder(ResponseInterface::class)
                                         ->disableOriginalConstructor()
                                         ->getMockForAbstractClass();

        $this->openAIModelsFactoryMock = $this->getMockBuilder(ModelsFactory::class)
                                              ->disableOriginalConstructor()
                                              ->getMock();

        $this->gpt3d5TurboModelMock = $this->getMockBuilder(ChatGPT3d5Model::class)
                                           ->disableOriginalConstructor()
                                           ->getMock();

        $this->reviewPorcessor = $this->objectManager->getObject(
            ReviewSummaryProcessor::class,
            [
                'requestFactory'  => $this->openAIRequestFactoryMock,
                'responseFactory' => $this->openAIResponseFactoryMock,
                'modelsFactory'   => $this->openAIModelsFactoryMock
            ]
        );

        parent::setUp();
    }

    /**
     * Method must always return the OpenAI Response object.
     *
     * @return void
     */
    public function testResponseReturned(): void
    {
        $prompt    = 'Test prompt!';
        $context   = ['dummy', 'context'];
        $modelType = 'gpt-3.5-turbo';

        $this->openAIRequestFactoryMock->expects($this->once())
                                       ->method('create')
                                       ->willReturn($this->openAIRequestMock);

        /** @var \MageWorx\OpenAI\Model\Options $options */
        $options = $this->objectManager->getObject(
            \MageWorx\OpenAI\Model\Options::class
        );
        $options->setModel($modelType);

        $response = $this->reviewPorcessor->execute($prompt, $context, $options);

        $this->assertInstanceOf(
            ResponseInterface::class,
            $response,
            'Method must always return the OpenAI Response object.'
        );
    }

    /**
     * The OpenAI Response object must have value inside.
     *
     * @return void
     */
    public function testResponseReturnedIsNotEmpty(): void
    {
        $prompt    = 'Test prompt!';
        $context   = ['dummy', 'context'];
        $modelType = 'gpt-3.5-turbo';

        $this->openAIRequestFactoryMock->expects($this->once())
                                       ->method('create')
                                       ->willReturn($this->openAIRequestMock);

        /** @var \MageWorx\OpenAI\Model\Options $options */
        $options = $this->objectManager->getObject(
            \MageWorx\OpenAI\Model\Options::class
        );
        $options->setModel($modelType);
        $this->openAIModelsFactoryMock->expects($this->once())
                                      ->method('create')
                                      ->with($modelType)
                                      ->willReturn($this->gpt3d5TurboModelMock);
        $this->gpt3d5TurboModelMock->expects($this->once())
                                   ->method('sendRequest')
                                   ->with($this->openAIRequestMock, $options)
                                   ->willReturn($this->openAIResponseMock);

        $this->openAIResponseMock->method('getContent')
                                 ->willReturn('Not empty content');

        $response = $this->reviewPorcessor->execute($prompt, $context, $options);

        $this->assertNotEmpty($response->getContent(), 'The OpenAI Response object must have value inside.');
    }

    /**
     * Model must be created during execute.
     *
     * @return void
     */
    public function testCorrectModelTypeCreation(): void
    {
        $prompt    = 'Test prompt!';
        $context   = ['dummy', 'context'];
        $modelType = 'gpt-3.5-turbo';

        /** @var \MageWorx\OpenAI\Model\Options $options */
        $options = $this->objectManager->getObject(
            \MageWorx\OpenAI\Model\Options::class
        );
        $options->setModel($modelType);
        $this->openAIModelsFactoryMock->expects($this->once())
                                      ->method('create')
                                      ->with($modelType)
                                      ->willReturn($this->gpt3d5TurboModelMock);

        $this->gpt3d5TurboModelMock->expects($this->atLeastOnce())
                                   ->method('sendRequest')
                                   ->with($this->openAIRequestMock, $options)
                                   ->willReturn($this->openAIResponseMock);

        $this->openAIRequestFactoryMock->expects($this->once())
                                       ->method('create')
                                       ->willReturn($this->openAIRequestMock);

        $response = $this->reviewPorcessor->execute($prompt, $context, $options);
    }

    /**
     * Model must be created during execute.
     *
     * @return void
     */
    public function testRequestGetContent(): void
    {
        $prompt    = 'Test prompt!';
        $context   = ['dummy', 'context'];
        $modelType = 'gpt-3.5-turbo';

        /** @var \MageWorx\OpenAI\Model\Options $options */
        $options = $this->objectManager->getObject(
            \MageWorx\OpenAI\Model\Options::class
        );
        $options->setModel($modelType);
        $this->openAIModelsFactoryMock->expects($this->once())
                                      ->method('create')
                                      ->with($modelType)
                                      ->willReturn($this->gpt3d5TurboModelMock);

        $this->openAIRequestFactoryMock->expects($this->once())
                                       ->method('create')
                                       ->willReturn($this->openAIRequestMock);

        $this->openAIRequestMock->expects($this->atLeastOnce())
                                ->method('setContent')
                                ->willReturnSelf();

        $this->gpt3d5TurboModelMock->expects($this->atLeastOnce())
                                   ->method('sendRequest')
                                   ->with($this->openAIRequestMock, $options)
                                   ->willReturn($this->openAIResponseMock);

        $response = $this->reviewPorcessor->execute($prompt, $context, $options);
    }

    /**
     * Execute generation with empty prompt content is not allowed, so we throw an error.
     *
     * @return void
     */
    public function testEmptyPromptContentException(): void
    {
        $prompt  = '';
        $context = ['dummy', 'context'];

        /** @var \MageWorx\OpenAI\Model\Options $options */
        $options = $this->objectManager->getObject(
            \MageWorx\OpenAI\Model\Options::class
        );

        $this->openAIModelsFactoryMock->expects($this->never())
                                      ->method('create');

        $this->openAIRequestFactoryMock->expects($this->never())
                                       ->method('create');

        $this->openAIRequestMock->expects($this->never())
                                ->method('setContent');

        $this->gpt3d5TurboModelMock->expects($this->never())
                                   ->method('sendRequest');

        $result = null;
        try {
            $response = $this->reviewPorcessor->execute($prompt, $context, $options);
        } catch (InputDataException $localizedException) {
            $result = $localizedException;
        }

        $this->assertNotEmpty($result, 'A Localized Exception must be thrown when the prompt CONTENT is empty!');
        $this->assertInstanceOf(InputDataException::class, $result, 'We must throw exactly LocalizedException because we show it to clients.');
        $this->assertStringContainsString('prompt is required to generate a summary', $result->getMessage(), 'It must be the Empty CONTENT exception.');
    }

    /**
     * Execute generation with empty prompt context (without at least one product review)
     * is not allowed, so we throw an error.
     *
     * @return void
     */
    public function testEmptyPromptContextException(): void
    {
        $prompt  = 'Test prompt content!';
        $context = [];

        /** @var \MageWorx\OpenAI\Model\Options $options */
        $options = $this->objectManager->getObject(
            \MageWorx\OpenAI\Model\Options::class
        );

        $this->openAIModelsFactoryMock->expects($this->never())
                                      ->method('create');

        $this->openAIRequestFactoryMock->expects($this->never())
                                       ->method('create');

        $this->openAIRequestMock->expects($this->never())
                                ->method('setContent');

        $this->gpt3d5TurboModelMock->expects($this->never())
                                   ->method('sendRequest');

        $result = null;
        try {
            $response = $this->reviewPorcessor->execute($prompt, $context, $options);
        } catch (InputDataException $localizedException) {
            $result = $localizedException;
        }

        $this->assertNotEmpty($result, 'A Localized Exception must be thrown when the prompt CONTEXT is empty!');
        $this->assertInstanceOf(InputDataException::class, $result, 'We must throw exactly LocalizedException because we show it to clients.');
        $this->assertStringContainsString('product must have at least one review', $result->getMessage(), 'It must be the Empty CONTEXT exception.');
    }
}
