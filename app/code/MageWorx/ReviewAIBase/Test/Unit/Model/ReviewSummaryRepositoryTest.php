<?php

namespace MageWorx\ReviewAIBase\Test\Unit\Model;

use Magento\Framework\Api\Search\FilterGroupBuilder;
use Magento\Framework\Api\SortOrderBuilder;
use MageWorx\ReviewAIBase\Api\Data\ReviewSummaryInterface;
use MageWorx\ReviewAIBase\Model\ReviewSummaryRepository;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use MageWorx\ReviewAIBase\Model\ResourceModel\ReviewSummary as ReviewSummaryResource;
use MageWorx\ReviewAIBase\Model\ReviewSummary;
use MageWorx\ReviewAIBase\Model\ResourceModel\ReviewSummary\Collection as ReviewSummaryCollection;
use MageWorx\ReviewAIBase\Model\ResourceModel\ReviewSummary\CollectionFactory as ReviewSummaryCollectionFactory;
use MageWorx\ReviewAIBase\Model\ReviewSummaryFactory;
use Magento\Framework\Api\SearchCriteria;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use MageWorx\ReviewAIBase\Api\Data\ReviewSummarySearchResultsInterface;
use MageWorx\ReviewAIBase\Api\Data\ReviewSummarySearchResultsInterfaceFactory as SearchResultsInterfaceFactory;

class ReviewSummaryRepositoryTest extends TestCase
{
    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * @var ReviewSummaryRepository
     */
    protected $reviewSummaryRepository;

    /**
     * @var ReviewSummaryResource|MockObject
     */
    protected $resource;

    /**
     * @var ReviewSummaryFactory|MockObject
     */
    protected $reviewSummaryFactory;

    /**
     * @var ReviewSummaryCollection|MockObject
     */
    protected $reviewSummaryCollection;

    /**
     * @var SearchCriteriaBuilder|MockObject
     */
    protected $searchCriteriaBuilder;

    /**
     * @var SearchResultsInterfaceFactory|MockObject
     */
    protected $searchResultsFactory;

    protected function setUp(): void
    {
        $this->objectManager = new ObjectManager($this);

        $this->resource = $this->getMockBuilder(ReviewSummaryResource::class)
                               ->disableOriginalConstructor()
                               ->getMock();

        $this->reviewSummaryFactory = $this->getMockBuilder(ReviewSummaryFactory::class)
                                           ->disableOriginalConstructor()
                                           ->getMock();

        $this->reviewSummaryCollection = $this->getMockBuilder(ReviewSummaryCollection::class)
                                              ->disableOriginalConstructor()
                                              ->getMock();

        $this->searchCriteriaBuilder = $this->getMockBuilder(SearchCriteriaBuilder::class)
                                            ->disableOriginalConstructor()
                                            ->getMock();

        $this->searchResultsFactory = $this->getMockBuilder(SearchResultsInterfaceFactory::class)
                                           ->disableOriginalConstructor()
                                           ->getMock();

        $this->searchResults = $this->getMockBuilder(ReviewSummarySearchResultsInterface::class)
                                    ->disableOriginalConstructor()
                                    ->getMockForAbstractClass();

        $this->reviewSummaryCollectionFactory = $this->getMockBuilder(ReviewSummaryCollectionFactory::class)
                                                     ->disableOriginalConstructor()
                                                     ->getMock();

        $this->filterGroupBuilder = $this->getMockBuilder(FilterGroupBuilder::class)
                                         ->disableOriginalConstructor()
                                         ->getMock();

        $this->sortOrderBuilder = $this->getMockBuilder(SortOrderBuilder::class)
                                       ->disableOriginalConstructor()
                                       ->getMock();

        $this->reviewSummaryRepository = $this->objectManager->getObject(
            ReviewSummaryRepository::class,
            [
                'resource'                       => $this->resource,
                'reviewSummaryFactory'           => $this->reviewSummaryFactory,
                'reviewSummaryCollectionFactory' => $this->reviewSummaryCollectionFactory,
                'searchCriteriaBuilder'          => $this->searchCriteriaBuilder,
                'searchResultsFactory'           => $this->searchResultsFactory,
                'filterGroupBuilder'             => $this->filterGroupBuilder,
                'sortOrderBuilder'               => $this->sortOrderBuilder,
            ]
        );

        parent::setUp();
    }

    /**
     * Dummy test for saving review summary
     *
     * @return void
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function testSaveReviewSummary(): void
    {
        $reviewSummaryData = [
            'product_id'   => 123,
            'summary_data' => 'Sample summary data',
            'status'       => 1,
            'store_id'     => 1,
        ];

        $reviewSummary = $this->getMockBuilder(ReviewSummary::class)
                              ->disableOriginalConstructor()
                              ->getMock();

        $reviewSummary->expects($this->once())
                      ->method('getData')
                      ->willReturn($reviewSummaryData);

        $reviewSummary->expects($this->any())
                      ->method('setData')
                      ->with($reviewSummaryData)
                      ->willReturnSelf();

        $this->resource->expects($this->atLeastOnce())
            ->method('save')
            ->with($reviewSummary)
            ->willReturn($reviewSummary);

        $savedReviewSummary = $this->reviewSummaryRepository->save($reviewSummary);

        $this->assertInstanceOf(ReviewSummaryInterface::class, $savedReviewSummary);

        $savedReviewSummaryData = $savedReviewSummary->getData();

        $this->assertEquals($reviewSummaryData['product_id'], $savedReviewSummaryData['product_id']);
        $this->assertEquals($reviewSummaryData['summary_data'], $savedReviewSummaryData['summary_data']);
        $this->assertEquals($reviewSummaryData['status'], $savedReviewSummaryData['status']);
        $this->assertEquals($reviewSummaryData['store_id'], $savedReviewSummaryData['store_id']);
    }

    public function testGetReviewSummaryById(): void
    {
        $reviewSummaryData = [
            'entity_id'    => 1,
            'product_id'   => 123,
            'summary_data' => 'Sample summary data',
            'status'       => 1,
            'store_id'     => 1,
        ];

        $reviewSummary = $this->createMock(ReviewSummary::class);

        $reviewSummary->expects($this->atLeastOnce())
                      ->method('getId')
                      ->willReturn($reviewSummaryData['entity_id']);

        $reviewSummary->expects($this->atLeastOnce())
                      ->method('getProductId')
                      ->willReturn($reviewSummaryData['product_id']);

        $reviewSummary->expects($this->atLeastOnce())
                      ->method('getSummaryData')
                      ->willReturn($reviewSummaryData['summary_data']);

        $reviewSummary->expects($this->atLeastOnce())
                      ->method('getStatus')
                      ->willReturn($reviewSummaryData['status']);

        $reviewSummary->expects($this->atLeastOnce())
                      ->method('getStoreId')
                      ->willReturn($reviewSummaryData['store_id']);

        $this->resource->expects($this->once())
                       ->method('load')
                       ->with($reviewSummary, $reviewSummaryData['entity_id'])
                       ->willReturnSelf();

        $this->reviewSummaryFactory->expects($this->once())
            ->method('create')
            ->willReturn($reviewSummary);

        $resultReviewSummary = $this->reviewSummaryRepository->getById($reviewSummaryData['entity_id']);

        $this->assertInstanceOf(ReviewSummaryInterface::class, $resultReviewSummary);

        $this->assertEquals($reviewSummaryData['entity_id'], $resultReviewSummary->getId());
        $this->assertEquals($reviewSummaryData['product_id'], $resultReviewSummary->getProductId());
        $this->assertEquals($reviewSummaryData['summary_data'], $resultReviewSummary->getSummaryData());
        $this->assertEquals($reviewSummaryData['status'], $resultReviewSummary->getStatus());
        $this->assertEquals($reviewSummaryData['store_id'], $resultReviewSummary->getStoreId());
    }

    public function testDeleteReviewSummary(): void
    {
        $reviewSummary = $this->createMock(ReviewSummary::class);

        $this->resource->expects($this->once())
                       ->method('delete')
                       ->with($reviewSummary);

        $result = $this->reviewSummaryRepository->delete($reviewSummary);

        $this->assertTrue($result);
    }

    public function testDeleteReviewSummaryById()
    {
        $entityId = 1;

        $reviewSummary = $this->createMock(ReviewSummary::class);

        $reviewSummary->expects($this->atLeastOnce())
            ->method('getId')
            ->willReturn($entityId);

        $this->reviewSummaryFactory->expects($this->once())
                                   ->method('create')
                                   ->willReturn($reviewSummary);

        $this->resource->expects($this->once())
                       ->method('load')
                       ->with($reviewSummary, $entityId)
                       ->willReturnSelf();

        $this->resource->expects($this->once())
                       ->method('delete')
                       ->with($reviewSummary);

        $result = $this->reviewSummaryRepository->deleteById($entityId);

        $this->assertTrue($result);
    }

    public function testGetReviewSummaryList()
    {
        $searchCriteria = $this->getMockBuilder(SearchCriteriaInterface::class)
                               ->disableOriginalConstructor()
                               ->getMockForAbstractClass();

        $this->reviewSummaryCollectionFactory->expects($this->once())
                                             ->method('create')
                                             ->willReturn($this->reviewSummaryCollection);

        $this->searchResultsFactory->expects($this->once())
                                   ->method('create')
                                   ->willReturn($this->searchResults);

        $filterGroups = [];
        $sortOrder    = [];
        $pageSize     = 10;
        $currentPage  = 1;

        $searchCriteria->expects($this->once())
                       ->method('getFilterGroups')
                       ->willReturn($filterGroups);
        $searchCriteria->expects($this->once())
                       ->method('getSortOrders')
                       ->willReturn($sortOrder);
        $searchCriteria->expects($this->once())
                       ->method('getPageSize')
                       ->willReturn($pageSize);
        $searchCriteria->expects($this->once())
                       ->method('getCurrentPage')
                       ->willReturn($currentPage);

        $items = [];
        $size  = 0;
        $this->reviewSummaryCollection->expects($this->once())
                                      ->method('getItems')
                                      ->willReturn($items);
        $this->reviewSummaryCollection->expects($this->once())
                                      ->method('getSize')
                                      ->willReturn($size);

        $searchResults = $this->reviewSummaryRepository->getList($searchCriteria);

        $this->assertInstanceOf(ReviewSummarySearchResultsInterface::class, $searchResults);
    }
}
