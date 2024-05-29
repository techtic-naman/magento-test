<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace MageWorx\SocialProofBase\Model;

use Magento\Framework\ObjectManagerInterface;
use MageWorx\SocialProofBase\Api\CurrentEntityIdResolverInterface;

class CurrentEntityIdResolverFactory
{
    /**
     * Object Manager instance
     *
     * @var ObjectManagerInterface
     */
    protected $objectManager = null;

    /**
     * Instance name to create
     *
     * @var string
     */
    protected $map;

    /**
     * CurrentEntityIdResolverFactory constructor.
     *
     * @param ObjectManagerInterface $objectManager
     * @param array $map
     */
    public function __construct(
        ObjectManagerInterface $objectManager,
        array $map = []
    ) {
        $this->objectManager = $objectManager;
        $this->map           = $map;
    }

    /**
     * Create new instance
     *
     * @param string $param
     * @param array $arguments
     * @return CurrentEntityIdResolverInterface
     * @throws \UnexpectedValueException
     */
    public function create($param, array $arguments = [])
    {
        if (isset($this->map[$param])) {
            $instance = $this->objectManager->create($this->map[$param], $arguments);
        } else {
            $instance = $this->objectManager->create(
                \MageWorx\SocialProofBase\Model\CurrentEntityIdResolver\Regular::class,
                $arguments
            );
        }

        if (!$instance instanceof CurrentEntityIdResolverInterface) {
            throw new \UnexpectedValueException(
                'Class ' . get_class($instance) .
                ' should be an instance of ' . CurrentEntityIdResolverInterface::class
            );
        }

        return $instance;
    }
}
