<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace MageWorx\SocialProofBase\Api;

interface CurrentEntityIdResolverInterface
{
    /**
     * Get Entity ID.
     *
     * @return int|null
     */
    public function getEntityId(): ?int;
}
