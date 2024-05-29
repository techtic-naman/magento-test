<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types = 1);

namespace MageWorx\ReviewAIBase\Test\Unit\Model\AdditionalDataProcessor;

use PHPUnit\Framework\TestCase;
use Magento\Review\Model\Review;
use MageWorx\ReviewAIBase\Model\AdditionalDataProcessor\LocationState;

class LocationStateTest extends TestCase
{
    /**
     * @var LocationState
     */
    protected $model;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|Review
     */
    protected $reviewMock;

    protected function setUp(): void
    {
        $this->reviewMock = $this->createMock(Review::class);
        $this->model      = new LocationState();
    }

    public function testProcessWithRegion(): void
    {
        $this->reviewMock->expects($this->once())
                         ->method('getData')
                         ->with('region')
                         ->willReturn('Texas');

        $this->assertEquals('Texas', $this->model->process($this->reviewMock));
    }

    public function testProcessWithoutRegion(): void
    {
        $this->reviewMock->expects($this->once())
                         ->method('getData')
                         ->with('region')
                         ->willReturn('');

        $this->assertEquals('Not Defined', $this->model->process($this->reviewMock));
    }

    public function testProcessWithNullRegion(): void
    {
        $this->reviewMock->expects($this->once())
                         ->method('getData')
                         ->with('region')
                         ->willReturn(null);

        $this->assertEquals('Not Defined', $this->model->process($this->reviewMock));
    }
}

