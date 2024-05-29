<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace MageWorx\SocialProofBase\Model\Source\Campaign;

class DisplayOn extends \MageWorx\SocialProofBase\Model\Source
{
    const CMS_PAGES      = 'cms-pages';
    const PRODUCT_PAGES  = 'product-pages';
    const CATEGORY_PAGES = 'category-pages';

    /**
     * @return array
     */
    public function toOptionArray(): array
    {
        return [
            [
                'value' => self::PRODUCT_PAGES,
                'label' => __('Product Pages')
            ],
            [
                'value' => self::CATEGORY_PAGES,
                'label' => __('Category Pages')
            ],
            [
                'value' => self::CMS_PAGES,
                'label' => __('CMS Pages')
            ]
        ];
    }

    /**
     * @return array
     */
    public function getPageTypes(): array
    {
        return [
            'catalog_product_view'  => self::PRODUCT_PAGES,
            'catalog_category_view' => self::CATEGORY_PAGES,
            'cms_page_view'         => self::CMS_PAGES,
            'cms_index_index'       => self::CMS_PAGES
        ];
    }
}
