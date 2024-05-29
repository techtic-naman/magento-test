<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace MageWorx\SocialProofBase\Model;

use Magento\Catalog\Helper\Image as ImageHelper;
use Magento\Framework\DataObject;
use Magento\Framework\Event\ManagerInterface as EventManagerInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Review\Model\ReviewFactory;
use Magento\Framework\Escaper;

abstract class ConverterAbstract implements \MageWorx\SocialProofBase\Api\ConverterInterface
{
    /**
     * @var array
     */
    protected $allowedVars = [
        'product.name',
        'product.image',
        'product.url',
        'product.rating_summary',
        'product.rating_stars',
        'period',
        'count_customers',
        'count_times'
    ];

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var ImageHelper
     */
    protected $imageHelper;

    /**
     * Review model factory
     *
     * @var ReviewFactory
     */
    protected $reviewFactory;

    /**
     * @var EventManagerInterface
     */
    protected $eventManager;

    /**
     * @var Escaper
     */
    protected $escaper;

    /**
     * ConverterAbstract constructor.
     *
     * @param StoreManagerInterface $storeManager
     * @param ProductRepositoryInterface $productRepository
     * @param ImageHelper $imageHelper
     * @param ReviewFactory $reviewFactory
     * @param EventManagerInterface $eventManager
     * @param Escaper $escaper
     * @param array $data
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        ProductRepositoryInterface $productRepository,
        ImageHelper $imageHelper,
        ReviewFactory $reviewFactory,
        EventManagerInterface $eventManager,
        Escaper $escaper,
        $data = []
    ) {
        $this->storeManager      = $storeManager;
        $this->productRepository = $productRepository;
        $this->imageHelper       = $imageHelper;
        $this->reviewFactory     = $reviewFactory;
        $this->eventManager      = $eventManager;
        $this->escaper           = $escaper;

        if (!empty($data['custom_vars'])) {
            $this->allowedVars = array_merge($this->allowedVars, $data['custom_vars']);
        }
    }

    /**
     * @return array
     */
    public function getAllowedVars(): array
    {
        return $this->allowedVars;
    }

    /**
     * @param string $template
     * @return array
     */
    protected function getVarsFromTemplate($template): array
    {
        $vars = $this->parse($template);

        if (!empty($vars)) {
            $vars = array_intersect($this->allowedVars, $vars);
        }

        return $vars;
    }

    /**
     * @param string $str
     * @return array
     */
    protected function parse($str): array
    {
        $vars = [];

        if (preg_match_all('/\[(.*?)\]/', $str, $matches)) {
            $vars = $matches[1];
        }

        return $vars;
    }

    /**
     * @param int $productId
     * @return string
     * @throws NoSuchEntityException
     */
    protected function getProductName($productId): string
    {
        $product = $this->productRepository->getById($productId);

        return $this->escaper->escapeHtml($product->getName());
    }

    /**
     * @param int $productId
     * @return string
     * @throws NoSuchEntityException
     */
    protected function getProductImageUrl($productId): string
    {
        $product = $this->productRepository->getById($productId);

        return $this->imageHelper->init($product, 'product_small_image')->getUrl();
    }

    /**
     * @param int $productId
     * @return string
     * @throws NoSuchEntityException
     */
    protected function getProductUrl($productId): string
    {
        $product = $this->productRepository->getById($productId);

        return $product->getProductUrl();
    }

    /**
     * @param int $productId
     * @return int
     * @throws NoSuchEntityException
     */
    protected function getProductRatingSummary($productId): int
    {
        $product = $this->productRepository->getById($productId);

        if (!$product->getRatingSummary()) {
            $this->reviewFactory->create()->getEntitySummary($product, $this->storeManager->getStore()->getId());
        }

        $reviewDataObject = $product->getRatingSummary();

        if (!is_object($reviewDataObject) || !$reviewDataObject->getData()) {

            return 0;
        }

        $reviewData = $reviewDataObject->getData();

        if (empty($reviewData['reviews_count'])) {

            return 0;
        }

        return (int)$reviewData['rating_summary'];
    }

    /**
     * @param int $productId
     * @return float
     * @throws NoSuchEntityException
     */
    protected function getProductRatingStars($productId): float
    {
        $ratingSummary = $this->getProductRatingSummary($productId);

        return round(($ratingSummary / (100 / 5)), 1);
    }

    /**
     * @param int $period
     * @return string
     */
    protected function getPeriodStr($period): string
    {
        if ($period === 1) {
            return __('day')->__toString();
        }

        return $period . ' ' . __('days');
    }

    /**
     * @param int $count
     * @return string
     */
    protected function getCountCustomersStr($count): string
    {
        if ($count === 1) {
            return $count . ' ' . __('customer');
        }

        return $count . ' ' . __('customers');
    }

    /**
     * @param int $count
     * @return string
     */
    protected function getCountTimesStr($count): string
    {
        if ($count === 1) {
            return $count . ' ' . __('time');
        }

        return $count . ' ' . __('times');
    }

    /**
     * @param string $name
     * @param string $value
     * @return DataObject
     */
    protected function getTemplateVarContainer($name, $value): DataObject
    {
        $data = [
            'name'  => $name,
            'value' => $value
        ];

        $templateVarContainer = new DataObject();
        $templateVarContainer->setData($data);

        return $templateVarContainer;
    }
}
