<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types = 1);

namespace MageWorx\ReviewAIBase\Test\Unit\Ui\DataProvider\Product\Form\Modifier;

use Magento\Backend\Model\UrlInterface;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\App\Request\Http as RequestHTTP;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Model\StoreManagerInterface;
use MageWorx\ReviewAIBase\Api\ReviewSummaryRepositoryInterface;
use MageWorx\ReviewAIBase\Ui\DataProvider\Product\Form\Modifier\SummaryData;
use PHPUnit\Framework\TestCase;

/**
 * Test class for \MageWorx\ReviewAIBase\Ui\DataProvider\Product\Form\Modifier\SummaryData.
 *
 * This test class aims to verify the functionality of the SummaryData modifier within the Magento UI data provider framework.
 * It checks the following scenarios:
 * - The modification of product data based on review summaries.
 * - Handling cases with empty or incomplete product data.
 * - The inclusion of UI components for displaying review summary data in the product edit form.
 *
 * @coversDefaultClass \MageWorx\ReviewAIBase\Ui\DataProvider\Product\Form\Modifier\SummaryData
 */
class SummaryDataTest extends TestCase
{
    private $reviewSummaryRepositoryMock;
    private $searchCriteriaBuilderMock;
    private $searchCriteriaMock;
    private $filterBuilderMock;
    private $summaryData;
    private $storeManagerMock;
    private $storeMock;
    private $urlBuilderMock;
    private $requestHttpMock;

    protected function setUp(): void
    {
        $this->reviewSummaryRepositoryMock = $this->getMockForAbstractClass(ReviewSummaryRepositoryInterface::class);
        $this->searchCriteriaBuilderMock   = $this->createMock(SearchCriteriaBuilder::class);
        $this->filterBuilderMock           = $this->createMock(FilterBuilder::class);
        $this->storeManagerMock            = $this->getMockForAbstractClass(StoreManagerInterface::class);
        $this->storeMock                   = $this->getMockForAbstractClass(StoreInterface::class);
        $this->urlBuilderMock              = $this->getMockForAbstractClass(UrlInterface::class);
        $this->requestHttpMock             = $this->createMock(RequestHTTP::class);

        $this->searchCriteriaMock = $this->getMockBuilder(\Magento\Framework\Api\SearchCriteria::class)
                                         ->disableOriginalConstructor()
                                         ->getMock();

        $this->searchCriteriaBuilderMock->method('create')
                                        ->willReturn($this->searchCriteriaMock);

        $this->summaryData = new SummaryData(
            $this->reviewSummaryRepositoryMock,
            $this->searchCriteriaBuilderMock,
            $this->filterBuilderMock,
            $this->requestHttpMock,
            $this->urlBuilderMock,
            $this->storeManagerMock
        );
    }

    /**
     * Tests the modifyData method with empty product data.
     *
     * We want to ensure that if the product data provided does not have the required 'store_id',
     * the method should simply continue without any errors, returning the original data unmodified.
     *
     * @covers \MageWorx\ReviewAIBase\Ui\DataProvider\Product\Form\Modifier\SummaryData::modifyData
     */
    public function testModifyDataWithEmptyProductData(): void
    {
        $productId = 123;
        $storeId   = null;
        $inputData = [
            $productId => ['product' => []]
        ];

        $this->storeManagerMock->expects($this->once())->method('getStore')->willReturn($this->storeMock);
        $this->storeMock->expects($this->once())->method('getId')->willReturn($storeId);
        // Stub for searchCriteriaBuilder to ensure filters are not added.
        $this->searchCriteriaBuilderMock->expects($this->never())->method('addFilters');

        // A stub for reviewSummaryRepository to ensure that no requests are made to the repository.
        $this->reviewSummaryRepositoryMock->expects($this->never())->method('getList');

        $outputData = $this->summaryData->modifyData($inputData);

        $this->assertEquals($inputData, $outputData, "Output data should be the same as input data when 'store_id' is missing.");
    }

    /**
     * Test the modifyData method with valid product data.
     *
     * Given a product data array with valid 'store_id',
     * the method should make a call to the review summary repository.
     * If a review summary is found, the product data should be updated
     * with the summary_data. Otherwise, product data should remain unchanged.
     *
     * @covers \MageWorx\ReviewAIBase\Ui\DataProvider\Product\Form\Modifier\SummaryData::modifyData
     */
    public function testModifyDataWithValidProductData(): void
    {
        $productId   = 123;
        $storeId     = 1;
        $summaryData = "Sample summary data";
        $inputData   = [
            $productId => ['product' => []]
        ];

        $this->storeManagerMock->expects($this->once())->method('getStore')->willReturn($this->storeMock);
        $this->storeMock->expects($this->once())->method('getId')->willReturn($storeId);

        $this->requestHttpMock->expects($this->atLeastOnce())
                              ->method('getParam')
                              ->with('id')
                              ->willReturn($productId);

        // Mock the filter and search criteria.
        $mockFilter1 = $this->createMock(\Magento\Framework\Api\Filter::class);
        $mockFilter2 = $this->createMock(\Magento\Framework\Api\Filter::class);
        $this->filterBuilderMock->expects($this->exactly(2))
                                ->method('setField')
                                ->withConsecutive(['product_id'], ['store_id'])
                                ->willReturnSelf();

        $this->filterBuilderMock->expects($this->exactly(2))
                                ->method('setValue')
                                ->withConsecutive([$productId], [$storeId])
                                ->willReturnSelf();

        $this->filterBuilderMock->expects($this->exactly(2))
                                ->method('setConditionType')
                                ->with('eq')
                                ->willReturnSelf();

        $this->filterBuilderMock->expects($this->exactly(2))
                                ->method('create')
                                ->willReturnOnConsecutiveCalls($mockFilter1, $mockFilter2);

        // Mock the search results and returned item.
        $mockItem = $this->createMock(\MageWorx\ReviewAIBase\Api\Data\ReviewSummaryInterface::class);
        $mockItem->expects($this->once())
                 ->method('getSummaryData')
                 ->willReturn($summaryData);

        $mockSearchResults = $this->createMock(\MageWorx\ReviewAIBase\Api\Data\ReviewSummarySearchResultsInterface::class);
        $mockSearchResults->expects($this->once())
                          ->method('getTotalCount')
                          ->willReturn(1);
        $mockSearchResults->expects($this->once())
                          ->method('getItems')
                          ->willReturn([$mockItem]);

        $this->reviewSummaryRepositoryMock->expects($this->once())
                                          ->method('getList')
                                          ->willReturn($mockSearchResults);

        $outputData = $this->summaryData->modifyData($inputData);

        $this->assertArrayHasKey('summary_data', $outputData[$productId]['product']);
        $this->assertEquals($summaryData, $outputData[$productId]['product']['summary_data']);
    }

    /**
     * Tests the modifyMeta method.
     *
     * We want to ensure that the method appends the necessary UI components for the 'Review Summary Data'
     * to the given meta data array. This test checks if the expected keys and structures are present
     * in the returned modified meta.
     *
     * @covers \MageWorx\ReviewAIBase\Ui\DataProvider\Product\Form\Modifier\SummaryData::modifyMeta
     */
    public function testModifyMeta(): void
    {
        $originalMeta  = [
            'some-other-data' => []
        ];
        $storeId       = 1;
        $urlGenerate   = 'mageworx_reviewai/reviewsummary/GenerateForProduct';
        $urlAddToQueue = 'mageworx_reviewai/reviewsummary/AddProductToQueue';

        $this->storeManagerMock->expects($this->once())->method('getStore')->willReturn($this->storeMock);
        $this->storeMock->expects($this->once())->method('getId')->willReturn($storeId);
        $this->urlBuilderMock->expects($this->exactly(2))
                             ->method('getUrl')
                             ->withConsecutive(
                                 [$urlGenerate],
                                 [$urlAddToQueue]
                             )
                             ->willReturnOnConsecutiveCalls(
                                 'some_url_1',
                                 'some_url_2'
                             );

        $this->requestHttpMock->expects($this->atLeastOnce())
                              ->method('getParam')
                              ->with('id')
                              ->willReturn(1);

        $modifier = new SummaryData(
            $this->reviewSummaryRepositoryMock,
            $this->searchCriteriaBuilderMock,
            $this->filterBuilderMock,
            $this->requestHttpMock,
            $this->urlBuilderMock,
            $this->storeManagerMock
        );

        $modifiedMeta = $modifier->modifyMeta($originalMeta);

        $this->assertIsArray($modifiedMeta, 'Modified meta should be an array');

        // Check if our expected keys are present
        $this->assertArrayHasKey('review_summary_fieldset', $modifiedMeta, 'review_summary_fieldset key missing in meta');
        $this->assertArrayHasKey('children', $modifiedMeta['review_summary_fieldset'], 'children key missing in review_summary_fieldset');
        $this->assertArrayHasKey('summary_data', $modifiedMeta['review_summary_fieldset']['children'], 'summary_data key missing in children');
        $this->assertArrayHasKey('buttons_container', $modifiedMeta['review_summary_fieldset']['children'], 'buttons_container key missing in children');

        $containerSummaryData = $modifiedMeta['review_summary_fieldset']['children']['summary_data'];
        $this->assertArrayHasKey('arguments', $containerSummaryData, 'arguments key missing in summary_data');

        $buttonsContainer = $modifiedMeta['review_summary_fieldset']['children']['buttons_container'];
        $this->assertArrayHasKey('arguments', $buttonsContainer, 'arguments key missing in buttons_container');
        $this->assertArrayHasKey('children', $buttonsContainer, 'children key missing in buttons_container');
        $this->assertArrayHasKey('generate_button', $buttonsContainer['children'], 'generate_button key missing in children of buttons_container');
        $this->assertArrayHasKey('add_to_queue_button', $buttonsContainer['children'], 'add_to_queue_button key missing in children of buttons_container');
    }
}

