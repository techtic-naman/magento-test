<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types = 1);

namespace MageWorx\ReviewAIBase\Test\Unit\Model\AdditionalDataProcessor;

use PHPUnit\Framework\TestCase;
use Magento\Review\Model\Review;
use MageWorx\ReviewAIBase\Model\AdditionalDataProcessor\IsRecommended;

class IsRecommendTest extends TestCase
{
    /**
     * @var IsRecommended
     */
    protected $model;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|Review
     */
    protected $reviewMock;

    protected function setUp(): void
    {
        $this->reviewMock = $this->createMock(Review::class);

        $this->model = new IsRecommended();
    }

    /**
     * Test process method when is_recommend is true
     *
     * @covers \MageWorx\ReviewAIBase\Model\AdditionalDataProcessor\IsRecommended::process
     * @return void
     */
    public function testRecommended(): void
    {
        $this->reviewMock->method('getData')
                         ->with('is_recommend')
                         ->willReturn(true);

        $this->assertEquals('Yes', $this->model->process($this->reviewMock));
    }

    /**
     * Test process method when is_recommend is false
     *
     * @covers \MageWorx\ReviewAIBase\Model\AdditionalDataProcessor\IsRecommended::process
     * @return void
     */
    public function testNotRecommended(): void
    {
        $this->reviewMock->method('getData')
                         ->with('is_recommend')
                         ->willReturn(false);

        $this->assertEquals('No', $this->model->process($this->reviewMock));
    }
}
