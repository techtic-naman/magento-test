<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace MageWorx\SocialProofBase\Model\CurrentEntityIdResolver;

use Magento\Cms\Model\Page;

class CmsPage implements \MageWorx\SocialProofBase\Api\CurrentEntityIdResolverInterface
{
    /**
     * @var Page
     */
    protected $page;

    /**
     * HomePage constructor.
     *
     * @param Page $page
     */
    public function __construct(Page $page)
    {
        $this->page = $page;
    }

    /**
     * @return int|null
     */
    public function getEntityId(): ?int
    {
        $pageId = (int)$this->page->getId();

        return $pageId ?: null;
    }
}
