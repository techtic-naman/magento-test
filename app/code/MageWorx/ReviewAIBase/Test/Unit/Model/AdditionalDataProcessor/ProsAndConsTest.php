<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types = 1);

namespace MageWorx\ReviewAIBase\Test\Unit\Model\AdditionalDataProcessor;

use MageWorx\ReviewAIBase\Model\AdditionalDataProcessor\Cons;
use PHPUnit\Framework\TestCase;
use Magento\Review\Model\Review;
use MageWorx\ReviewAIBase\Model\AdditionalDataProcessor\Pros;

class ProsAndConsTest extends TestCase
{
    /**
     * @var Pros
     */
    protected $prosModel;

    /**
     * @var Cons
     */
    protected $consModel;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|Review
     */
    protected $reviewMock;

    protected function setUp(): void
    {
        $this->reviewMock = $this->createMock(Review::class);
        $this->prosModel  = new Pros();
        $this->consModel  = new Cons();
    }

    public function testProcessOnlyPros(): void
    {
        $this->reviewMock->method('getData')
                         ->with('pros')
                         ->willReturn('Good quality');

        $this->assertEquals('Good quality', $this->prosModel->process($this->reviewMock));
    }

    public function testProcessWithNoPros(): void
    {
        $this->reviewMock->method('getData')
                         ->with('pros')
                         ->willReturn(null);

        $this->assertEquals('Not Defined', $this->prosModel->process($this->reviewMock));
    }

    public function testProcessOnlyCons(): void
    {
        $this->reviewMock->method('getData')
                         ->with('cons')
                         ->willReturn('Too expensive');

        $this->assertEquals('Too expensive', $this->consModel->process($this->reviewMock));
    }

    public function testProcessWithNoCons(): void
    {
        $this->reviewMock->method('getData')
                         ->with('cons')
                         ->willReturn(null);

        $this->assertEquals('Not Defined', $this->consModel->process($this->reviewMock));
    }
}

