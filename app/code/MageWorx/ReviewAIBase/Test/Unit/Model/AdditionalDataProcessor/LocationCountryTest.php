<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types = 1);

namespace MageWorx\ReviewAIBase\Test\Unit\Model\AdditionalDataProcessor;

use PHPUnit\Framework\TestCase;
use Magento\Review\Model\Review;
use MageWorx\ReviewAIBase\Model\AdditionalDataProcessor\LocationCountry;

class LocationCountryTest extends TestCase
{
    /**
     * @var LocationCountry
     */
    protected $model;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|Review
     */
    protected $reviewMock;

    protected function setUp(): void
    {
        $this->reviewMock = $this->createMock(Review::class);
        $this->model      = new LocationCountry();
    }

    public function testProcessWithLocationCode(): void
    {
        $this->reviewMock->expects($this->once())
                         ->method('getData')
                         ->with('location')
                         ->willReturn('US');

        $this->assertEquals('US', $this->model->process($this->reviewMock));
    }

    public function testProcessWithoutLocationCode(): void
    {
        $this->reviewMock->expects($this->once())
                         ->method('getData')
                         ->with('location')
                         ->willReturn('');

        $this->assertEquals('Not Defined', $this->model->process($this->reviewMock));
    }

    public function testProcessWithNullLocationCode(): void
    {
        $this->reviewMock->expects($this->once())
                         ->method('getData')
                         ->with('location')
                         ->willReturn(null);

        $this->assertEquals('Not Defined', $this->model->process($this->reviewMock));
    }
}
