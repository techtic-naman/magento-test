<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\RewardPoints\Model\Rule;

use Magento\Framework\ObjectManagerInterface as ObjectManager;

/**
 * Factory class
 *
 * @see \MageWorx\RewardPoints\Model\Rule\CalculationStrategy
 */
class CalculationStrategyFactory
{
    /**
     * Object Manager instance
     *
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $objectManager = null;

    /**
     * Instance name to create
     *
     * @var string
     */
    protected $map;

    /**
     * Factory constructor
     *
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param array $map
     */

    public function __construct(
        ObjectManager $objectManager,
        array $map = []
    ) {
        $this->objectManager = $objectManager;
        $this->map           = $map;
    }

    /**
     *
     * @param string $param
     * @param array $arguments
     * @return \MageWorx\RewardPoints\Model\Rule\CalculationStrategyInterface
     * @throws \UnexpectedValueException
     */
    public function create($param, array $arguments = [])
    {
        if (isset($this->map[$param])) {
            $instance = $this->objectManager->create($this->map[$param], $arguments);
        }

        if (!$instance instanceof \MageWorx\RewardPoints\Model\Rule\CalculationStrategyInterface) {
            throw new \UnexpectedValueException(
                __(
                    'Class %1 should be an instance of %2',
                    get_class($instance),
                    'MageWorx\RewardPoints\Model\Rule\CalculationStrategyInterface'
                )
            );
        }

        return $instance;
    }
}