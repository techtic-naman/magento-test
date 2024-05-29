<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types = 1);

namespace MageWorx\ReviewAIBase\Test\Unit\Model\AdditionalDataProcessor;

use PHPUnit\Framework\TestCase;
use Magento\Review\Model\Review;
use MageWorx\ReviewAIBase\Model\AdditionalDataProcessor\IsHelpful;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\DB\Select;

class IsHelpfulTest extends TestCase
{
    /**
     * @var IsHelpful
     */
    protected $model;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|Review
     */
    protected $reviewMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|ResourceConnection
     */
    protected $resourceConnectionMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|AdapterInterface
     */
    protected $connectionMock;

    protected function setUp(): void
    {
        $this->reviewMock             = $this->createMock(Review::class);
        $this->resourceConnectionMock = $this->createMock(ResourceConnection::class);
        $this->connectionMock         = $this->createMock(AdapterInterface::class);
        $this->selectMock             = $this->createMock(Select::class);

        $this->resourceConnectionMock->method('getConnection')
                                     ->willReturn($this->connectionMock);

        $this->model = new IsHelpful($this->resourceConnectionMock);
    }

    public function testProcessHelpful(): void
    {
        $reviewId = 123;
        $this->reviewMock->method('getId')->willReturn($reviewId);

        $this->connectionMock->method('fetchOne')
                             ->willReturn('1');
        $this->connectionMock->method('getTableName')
                             ->with('mageworx_xreviewbase_review_vote')
                             ->willReturn('mageworx_xreviewbase_review_vote');
        $this->connectionMock->method('isTableExists')
                             ->with('mageworx_xreviewbase_review_vote')
                             ->willReturn(true);
        $this->connectionMock->method('select')
                             ->willReturn($this->selectMock);
        $this->selectMock->method('from')
                         ->willReturnSelf();
        $this->selectMock->method('where')
                         ->willReturnSelf();

        $this->assertEquals('Yes', $this->model->process($this->reviewMock));
    }

    public function testProcessNotHelpful(): void
    {
        $reviewId = 123;
        $this->reviewMock->method('getId')->willReturn($reviewId);

        $this->connectionMock->method('fetchOne')
                             ->willReturn('-1');

        $this->connectionMock->method('getTableName')
                             ->with('mageworx_xreviewbase_review_vote')
                             ->willReturn('mageworx_xreviewbase_review_vote');
        $this->connectionMock->method('isTableExists')
                             ->with('mageworx_xreviewbase_review_vote')
                             ->willReturn(true);
        $this->connectionMock->method('select')
                             ->willReturn($this->selectMock);
        $this->selectMock->method('from')
                         ->willReturnSelf();
        $this->selectMock->method('where')
                         ->willReturnSelf();

        $this->assertEquals('No', $this->model->process($this->reviewMock));
    }

    public function testProcessNullValue(): void
    {
        $reviewId = 123;
        $this->reviewMock->method('getId')->willReturn($reviewId);

        $this->connectionMock->method('fetchOne')
                             ->willReturn('0');

        $this->connectionMock->method('getTableName')
                             ->with('mageworx_xreviewbase_review_vote')
                             ->willReturn('mageworx_xreviewbase_review_vote');
        $this->connectionMock->method('isTableExists')
                             ->with('mageworx_xreviewbase_review_vote')
                             ->willReturn(true);
        $this->connectionMock->method('select')
                             ->willReturn($this->selectMock);
        $this->selectMock->method('from')
                         ->willReturnSelf();
        $this->selectMock->method('where')
                         ->willReturnSelf();

        $this->assertEquals('Not Defined', $this->model->process($this->reviewMock));
    }

    /**
     * Test process method when mageworx_xreviewbase_review_vote table does not exist
     *
     * @covers \MageWorx\ReviewAIBase\Model\AdditionalDataProcessor\IsHelpful::process
     * @return void
     */
    public function testProcessWhenMageworxTableDoesNotExist(): void
    {
        $reviewId = 123;
        $this->reviewMock->method('getId')->willReturn($reviewId);

        $this->connectionMock->method('getTableName')
                             ->with('mageworx_xreviewbase_review_vote')
                             ->willReturn('mageworx_xreviewbase_review_vote');
        $this->connectionMock->method('isTableExists')
                             ->with('mageworx_xreviewbase_review_vote')
                             ->willReturn(false);

        $this->assertEquals('', $this->model->process($this->reviewMock));
    }
}
