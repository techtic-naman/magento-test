<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types = 1);

namespace MageWorx\ReviewAIBase\Test\Unit\Model\AdditionalDataProcessor;

use PHPUnit\Framework\TestCase;
use Magento\Review\Model\Review;
use MageWorx\ReviewAIBase\Model\AdditionalDataProcessor\IsVerifiedCustomer;

class IsVerifiedCustomerTest extends TestCase
{
    /**
     * @var IsVerifiedCustomer
     */
    protected $model;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|Review
     */
    protected $reviewMock;

    protected function setUp(): void
    {
        $this->reviewMock = $this->createMock(Review::class);
        $this->model      = new IsVerifiedCustomer();
    }

    public function testProcessVerified(): void
    {
        $this->reviewMock->expects($this->once())
                         ->method('getData')
                         ->with('is_verified')
                         ->willReturn(true);

        $this->assertEquals('Yes', $this->model->process($this->reviewMock));
    }

    public function testProcessUnverified(): void
    {
        $this->reviewMock->expects($this->once())
                         ->method('getData')
                         ->with('is_verified')
                         ->willReturn(false);

        $this->assertEquals('No', $this->model->process($this->reviewMock));
    }

    public function testProcessNull(): void
    {
        $this->reviewMock->expects($this->once())
                         ->method('getData')
                         ->with('is_verified')
                         ->willReturn(null);

        $this->assertEquals('Not Defined', $this->model->process($this->reviewMock));
    }
}

