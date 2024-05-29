<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace MageWorx\SocialProofBase\Model\CurrentEntityIdResolver;

class Regular implements \MageWorx\SocialProofBase\Api\CurrentEntityIdResolverInterface
{
    /**
     * @return int|null
     */
    public function getEntityId(): ?int
    {
        return null;
    }
}
