<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace MageWorx\SocialProofBase\Model\Source;

use Magento\Catalog\Model\Category as CategoryModel;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory as CategoryCollectionFactory;
use Magento\Framework\Exception\LocalizedException;

class CategoriesTree extends \MageWorx\SocialProofBase\Model\Source
{
    /**
     * @var CategoryCollectionFactory
     */
    protected $categoryCollectionFactory;

    /**
     * Options constructor.
     *
     * @param CategoryCollectionFactory $categoryCollectionFactory
     */
    public function __construct(CategoryCollectionFactory $categoryCollectionFactory)
    {
        $this->categoryCollectionFactory = $categoryCollectionFactory;
    }

    /**
     * To option array
     *
     * @return array
     * @throws LocalizedException
     */
    public function toOptionArray(): array
    {
        $categoriesTree = $this->retrieveCategoriesTree($this->retrieveCategoriesIds());

        return $categoriesTree ?: [];
    }

    /**
     * Retrieve tree of categories with attributes.
     *
     * @param array $categoriesIds
     * @return array|null
     * @throws LocalizedException
     */
    private function retrieveCategoriesTree(array $categoriesIds): ?array
    {
        /* @var $categoryCollection \Magento\Catalog\Model\ResourceModel\Category\Collection */
        $categoryCollection = $this->categoryCollectionFactory->create();

        $categoryCollection->addAttributeToFilter('entity_id', ['in' => array_keys($categoriesIds)])
                           ->addAttributeToSelect(['name', 'is_active', 'parent_id']);

        $categoryById = [
            CategoryModel::TREE_ROOT_ID => [
                'value'    => CategoryModel::TREE_ROOT_ID,
                'optgroup' => null
            ]
        ];

        /* @var $category CategoryModel */
        foreach ($categoryCollection as $category) {

            foreach ([$category->getId(), $category->getParentId()] as $categoryId) {

                if (!isset($categoryById[$categoryId])) {
                    $categoryById[$categoryId] = ['value' => $categoryId];
                }
            }

            $categoryById[$category->getId()]['label']            = $category->getName();
            $categoryById[$category->getId()]['is_active']        = $category->getIsActive();
            $categoryById[$category->getId()]['__disableTmpl']    = true;
            $categoryById[$category->getParentId()]['optgroup'][] = &$categoryById[$category->getId()];
        }

        return $categoryById[CategoryModel::TREE_ROOT_ID]['optgroup'];
    }


    /**
     * Retrieve list of categories id.
     *
     * @return array
     * @throws LocalizedException
     */
    private function retrieveCategoriesIds(): array
    {
        /* @var $categoryCollection \Magento\Catalog\Model\ResourceModel\Category\Collection */
        $categoryCollection = $this->categoryCollectionFactory->create();

        $categoryCollection
            ->addAttributeToSelect('path')
            ->addAttributeToFilter('entity_id', ['neq' => CategoryModel::TREE_ROOT_ID]);

        $categories    = $categoryCollection->getData();
        $categoriesIds = [];

        foreach ($categories as $category) {

            foreach (explode('/', $category[CategoryModel::KEY_PATH]) as $parentId) {
                $categoriesIds[$parentId] = 1;
            }
        }

        return $categoriesIds;
    }
}
