<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace MageWorx\SocialProofBase\Model\CurrentEntityIdResolver;

use Magento\Framework\App\RequestInterface;

class Product implements \MageWorx\SocialProofBase\Api\CurrentEntityIdResolverInterface
{
    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * Product constructor.
     *
     * @param RequestInterface $request
     */
    public function __construct(RequestInterface $request)
    {
        $this->request = $request;
    }

    /**
     * @return int|null
     */
    public function getEntityId(): ?int
    {
        $productId = (int)$this->request->getParam('id');

        return $productId ?: null;
    }
}
