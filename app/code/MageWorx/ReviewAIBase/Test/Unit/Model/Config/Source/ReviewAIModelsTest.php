<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types = 1);

namespace MageWorx\ReviewAIBase\Test\Unit\Model\Config\Source;

use MageWorx\OpenAI\Model\Source\OpenAIModels;
use MageWorx\ReviewAIBase\Model\Config\Source\ReviewAIModels;
use PHPUnit\Framework\TestCase;

/**
 * Class ReviewAIModelsTest
 *
 * Unit test for the ReviewAIModels class.
 * Tests the functionality of filtering OpenAI models based on allowed models.
 */
class ReviewAIModelsTest extends TestCase
{
    /**
     * @var ReviewAIModels
     */
    private ReviewAIModels $reviewAIModels;

    /**
     * @var array
     */
    private array $allowedModels = ['gpt-4', 'gpt-3.5-turbo'];

    /**
     * Sets up the test environment.
     *
     * Mocks the OpenAIModels class and prepares the ReviewAIModels instance.
     */
    protected function setUp(): void
    {
        // Mock the parent class OpenAIModels with predefined option array
        $openAIModels = $this->createMock(OpenAIModels::class);
        $openAIModels->method('toOptionArray')->willReturn(
            [
                ['value' => 'gpt-4', 'label' => 'gpt-4'],
                ['value' => 'gpt-3.5-turbo', 'label' => 'gpt-3.5-turbo'],
                ['value' => 'gpt-4-1106-preview', 'label' => 'gpt-4-1106-preview'],
                ['value' => 'gpt-3.5-turbo-16k', 'label' => 'gpt-3.5-turbo-16k']
            ]
        );

        $this->reviewAIModels = new ReviewAIModels(
            $this->allowedModels
        );
    }

    /**
     * Tests the toOptionArray method.
     *
     * Verifies that the method returns an array of options corresponding to allowed models.
     * Checks that each option contains the expected structure and values.
     */
    public function testToOptionArray()
    {
        $expected = [
            'gpt-3.5-turbo' => ['value' => 'gpt-3.5-turbo', 'label' => 'gpt-3.5-turbo'],
            'gpt-4'         => ['value' => 'gpt-4', 'label' => 'gpt-4']
        ];

        $result = $this->reviewAIModels->toOptionArray();
        $this->assertIsArray($result);
        $this->assertEquals(count($expected), count($result));

        foreach ($result as $value) {
            $this->assertArrayHasKey('value', $value);
            $this->assertArrayHasKey('label', $value);

            $model = $value['value'];
            $this->assertEquals($expected[$model]['value'], $value['value']);
            $this->assertEquals($expected[$model]['label'], $value['label']);
        }
    }
}
