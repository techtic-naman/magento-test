<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\RewardPoints\Model\Plugin;

use \Magento\Framework\EntityManager\TypeResolver;

/**
 * Class TypeResolverPlugin
 *
 * @todo    remove after MAGETWO-52608 resolved
 * @package MageWorx\RewardPoints\Model\Plugin
 */
class TypeResolverPlugin
{
    /**
     * @var array
     */
    private $typeMapping = [
        \MageWorx\RewardPoints\Model\Rule::class             => \MageWorx\RewardPoints\Api\Data\RuleInterface::class,
        \MageWorx\RewardPoints\Model\Rule\Interceptor::class => \MageWorx\RewardPoints\Api\Data\RuleInterface::class,
        \MageWorx\RewardPoints\Model\Rule\Proxy::class       => \MageWorx\RewardPoints\Api\Data\RuleInterface::class
    ];

    /**
     * @param TypeResolver $subject
     * @param              $result
     * @param              $type
     * @return mixed
     */
    public function afterResolve(\Magento\Framework\EntityManager\TypeResolver $subject, $result, $type)
    {
        $className = get_class($type);
        if (isset($this->typeMapping[$className])) {
            return $this->typeMapping[$className];
        }

        return $result;
    }
}
