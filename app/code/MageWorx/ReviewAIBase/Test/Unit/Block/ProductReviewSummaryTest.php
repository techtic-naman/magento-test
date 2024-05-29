<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\ReviewAIBase\Test\Unit\Block;

use Magento\Framework\Exception\NoSuchEntityException;
use MageWorx\ReviewAIBase\Block\ProductReviewSummary;
use MageWorx\ReviewAIBase\Helper\Config;
use MageWorx\ReviewAIBase\Model\ReviewSummary;
use MageWorx\ReviewAIBase\Model\ReviewSummaryLoader;
use Magento\Framework\Registry;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Catalog\Model\Product;

/**
 * Class ProductReviewSummaryTest
 *
 * This test class provides unit tests for the ProductReviewSummary block class.
 * It ensures the correct behavior of the methods responsible for fetching product
 * and store IDs, as well as the review summary data. The following test cases are included:
 *
 * - `testGetProductId`: Confirms that `getProductId` retrieves the correct product ID from the registry.
 * - `testGetProductIdWithNoProduct`: Ensures `getProductId` returns 0 when no product is currently set in the registry.
 * - `testGetStoreId`: Verifies that `getStoreId` returns the correct store ID from the store manager.
 * - `testGetSummaryReview`: Checks if `getSummaryReview` correctly uses the review summary loader to fetch
 *   and return summary data for the current product and store.
 * - `testGetSummaryReviewWithNoProduct`: Tests the behavior of `getSummaryReview` when there is no product detected,
 *    ensuring that it returns an empty string and the review summary loader is not invoked in such cases.
 * - `testGetSummaryReviewWithNoStoreId`: Tests `getSummaryReview` when `getStoreId` returns 0,
 *    ensuring an empty string is returned and the review summary loader is not invoked.
 *
 */
class ProductReviewSummaryTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var ProductReviewSummary
     */
    private $block;

    /**
     * @var ReviewSummaryLoader|\PHPUnit\Framework\MockObject\MockObject
     */
    private $reviewSummaryLoaderMock;

    /**
     * @var Registry|\PHPUnit\Framework\MockObject\MockObject
     */
    private $registryMock;

    /**
     * @var StoreManagerInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    private $storeManagerMock;

    /**
     * @var Context|\PHPUnit\Framework\MockObject\MockObject
     */
    private $contextMock;

    /**
     * @var StoreInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    private $storeMock;

    /**
     * @var Product|\PHPUnit\Framework\MockObject\MockObject
     */
    private $productMock;

    /**
     * @var Config|\PHPUnit\Framework\MockObject\MockObject
     */
    private $configMock;

    protected function setUp(): void
    {
        $objectManager                 = new ObjectManager($this);
        $this->reviewSummaryLoaderMock = $this->createMock(ReviewSummaryLoader::class);
        $this->registryMock            = $this->createMock(Registry::class);
        $this->storeManagerMock        = $this->createMock(StoreManagerInterface::class);
        $this->contextMock             = $this->createMock(Context::class);
        $this->storeMock               = $this->createMock(StoreInterface::class);
        $this->productMock             = $this->createMock(Product::class);
        $this->reviewSummaryEntityMock = $this->createMock(ReviewSummary::class);
        $this->configMock              = $this->createMock(Config::class);

        $this->storeManagerMock->method('getStore')->willReturn($this->storeMock);

        $this->block = $objectManager->getObject(
            ProductReviewSummary::class,
            [
                'reviewSummaryLoader' => $this->reviewSummaryLoaderMock,
                'registry'            => $this->registryMock,
                'storeManager'        => $this->storeManagerMock,
                'context'             => $this->contextMock,
                'config'              => $this->configMock,
                'data'                => []
            ]
        );
    }

    /**
     * Tests the `getProductId` method to ensure it returns the correct product ID.
     *
     * This test verifies that the `getProductId` method correctly retrieves the ID
     * of the current product from the registry. It asserts that the returned value
     * matches the expected product ID. The test ensures that the `registry` method
     * is called once with 'current_product' as an argument and that the `getId`
     * method on the product mock is also invoked once.
     *
     */
    public function testGetProductId(): void
    {
        $expectedProductId = 123;
        $this->registryMock->expects($this->once())
                           ->method('registry')
                           ->with('current_product')
                           ->willReturn($this->productMock);

        $this->productMock->expects($this->once())
                          ->method('getId')
                          ->willReturn($expectedProductId);

        $this->assertSame($expectedProductId, $this->block->getProductId());
    }

    /**
     * Tests the `getProductId` method when there is no current product.
     *
     * This test ensures that the `getProductId` method returns 0 when there is no
     * current product set in the registry. It asserts that the `registry` method is
     * called once with 'current_product' as an argument and that no product is
     * returned. The return value should default to 0, indicating no product.
     *
     */
    public function testGetProductIdWithNoProduct()
    {
        $this->registryMock->expects($this->once())
                           ->method('registry')
                           ->with('current_product')
                           ->willReturn(null);

        $this->assertSame(0, $this->block->getProductId());
    }

    /**
     * Tests the `getStoreId` method to confirm it returns the correct store ID.
     *
     * This test asserts that the `getStoreId` method returns the store ID
     * as expected. The test checks that the store manager's `getStore` method is
     * used to fetch the current store and that the store's `getId` method returns
     * the correct store ID.
     *
     */
    public function testGetStoreId(): void
    {
        $expectedStoreId = 1;
        $this->storeMock->method('getId')->willReturn($expectedStoreId);

        $this->assertSame($expectedStoreId, $this->block->getStoreId());
    }

    /**
     * Tests the `getSummaryReview` method to ensure it returns the correct summary.
     *
     * This test confirms that the `getSummaryReview` method retrieves the review
     * summary for the current product and store. It checks that the review summary
     * loader is called with the correct parameters and that the summary data
     * is returned as expected. The test asserts that the necessary methods are
     * called once and that the returned summary data matches the expected value.
     *
     */
    public function testGetSummaryReview(): void
    {
        $expectedProductId   = 123;
        $expectedStoreId     = 1;
        $expectedSummaryData = 'Summary Data';

        $this->registryMock->method('registry')->willReturn($this->productMock);
        $this->productMock->method('getId')->willReturn($expectedProductId);
        $this->storeMock->method('getId')->willReturn($expectedStoreId);
        $this->configMock->expects($this->once())->method('isEnabled')->willReturn(true);

        $this->reviewSummaryLoaderMock->expects($this->once())
                                      ->method('getByProductIdAndStoreId')
                                      ->with($expectedProductId, $expectedStoreId)
                                      ->willReturn($this->reviewSummaryEntityMock);

        $this->reviewSummaryEntityMock->expects($this->once())
                                      ->method('getSummaryData')
                                      ->willReturn($expectedSummaryData);

        $this->assertSame($expectedSummaryData, $this->block->getSummaryReview());
    }

    /**
     * Tests the `getSummaryReview` method when the product ID is not found.
     *
     * This test checks the behavior of `getSummaryReview` when no product is detected
     * and a product ID of 0 is returned. It ensures that when there is no current
     * product, the review summary loader is not called, and an empty string is
     * returned. This simulates the scenario where the review summary cannot be fetched
     * due to the absence of a valid product ID, and verifies that the method handles
     * such cases gracefully without causing errors.
     */
    public function testGetSummaryReviewWithNoProduct(): void
    {
        $expectedProductId   = 0; // No product found, so expecting ID to be 0
        $expectedSummaryData = ''; // Expecting an empty string when no product ID is present

        $this->registryMock->expects($this->once())
                           ->method('registry')
                           ->with('current_product')
                           ->willReturn($this->productMock);

        $this->productMock->expects($this->once())
                          ->method('getId')
                          ->willReturn($expectedProductId);

        $this->configMock->expects($this->once())->method('isEnabled')->willReturn(true);

        // ReviewSummaryLoader should not be called if product ID is 0
        $this->reviewSummaryLoaderMock->expects($this->never())
                                      ->method('getByProductIdAndStoreId');

        // Since there's no product, there should be no summary, hence expecting an empty string
        $this->assertSame($expectedSummaryData, $this->block->getSummaryReview());
    }

    /**
     * Tests the `getSummaryReview` method when the store ID is not found.
     *
     * This test checks the behavior of `getSummaryReview` when `getStoreId` returns 0.
     * It ensures that when there is no valid store ID, the review summary loader is not called,
     * and an empty string is returned. This simulates the scenario where the review summary
     * cannot be fetched due to the absence of a valid store ID and verifies that the method
     * handles such cases gracefully without causing errors.
     */
    public function testGetSummaryReviewWithNoStoreId(): void
    {
        $expectedProductId   = 123; // Assuming the product ID is found.
        $expectedStoreId     = 0;   // No store found, so expecting ID to be 0.
        $expectedSummaryData = '';  // Expecting an empty string when no store ID is present.

        // Setup the registry to return a product with the expected ID.
        $this->registryMock->method('registry')
                           ->willReturn($this->productMock);
        $this->productMock->method('getId')
                          ->willReturn($expectedProductId);

        // Mock the store manager to return a store with ID 0.
        $this->storeManagerMock->method('getStore')
                               ->willReturn($this->storeMock);
        $this->storeMock->method('getId')
                        ->willReturn($expectedStoreId);

        $this->configMock->expects($this->once())->method('isEnabled')->willReturn(true);

        // ReviewSummaryLoader should not be called if store ID is 0.
        $this->reviewSummaryLoaderMock->expects($this->never())
                                      ->method('getByProductIdAndStoreId');

        // Execute the method and verify the expected result.
        $this->assertSame($expectedSummaryData, $this->block->getSummaryReview());
    }

    /**
     * Tests the behavior of getSummaryReview method when the feature is disabled in the configuration.
     *
     * Ensures that if the feature toggle for review summaries is switched off, the method should not attempt to load review summaries
     * and should return an empty string. This test checks that the configuration is respected and that dependent methods are not called,
     * adhering to the principle of feature toggling.
     *
     * @return void
     * @throws NoSuchEntityException
     */
    public function testGetSummaryReviewWhenFeatureDisabledInConfig(): void
    {
        $expectedProductId   = 123; // Assuming the product ID is found.
        $expectedStoreId     = 1;   // Assuming the store ID is found.
        $expectedSummaryData = '';  // Expecting an empty string when disabled in config.

        // Setup the registry to return a product with the expected ID.
        $this->registryMock->method('registry')
                           ->willReturn($this->productMock);
        $this->productMock->method('getId')
                          ->willReturn($expectedProductId);

        // Mock the store manager to return a store with ID 1.
        $this->storeManagerMock->method('getStore')
                               ->willReturn($this->storeMock);
        $this->storeMock->method('getId')
                        ->willReturn($expectedStoreId);

        $this->configMock->expects($this->once())->method('isEnabled')->willReturn(false);

        // ReviewSummaryLoader should not be called if feature was disabled in config.
        $this->reviewSummaryLoaderMock->expects($this->never())
                                      ->method('getByProductIdAndStoreId');

        // Execute the method and verify the expected result (an empty string).
        $this->assertSame($expectedSummaryData, $this->block->getSummaryReview());
    }
}

