<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\RewardPoints\Api;

/**
 * Reward rule CRUD interface
 */
interface RuleRepositoryInterface
{
    /**
     * Save reward rule.
     *
     * @param \MageWorx\RewardPoints\Api\Data\RuleInterface $rule
     * @return \MageWorx\RewardPoints\Api\Data\RuleInterface
     * @throws \Magento\Framework\Exception\InputException If there is a problem with the input
     * @throws \Magento\Framework\Exception\NoSuchEntityException If a rule ID is sent but the rule does not exist
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(\MageWorx\RewardPoints\Api\Data\RuleInterface $rule);

    /**
     * Save reward rule using wrapper model.
     *
     * @param \\MageWorx\RewardPoints\Model\Rule $rule
     * @return \MageWorx\RewardPoints\Model\Rule
     * @throws \Magento\Framework\Exception\InputException If there is a problem with the input
     * @throws \Magento\Framework\Exception\NoSuchEntityException If a rule ID is sent but the rule does not exist
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function saveRule(\MageWorx\RewardPoints\Model\Rule $rule);

    /**
     * Get rule by ID.
     *
     * @param int $ruleId
     * @return \MageWorx\RewardPoints\Api\Data\RuleInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException If $id is not found
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($ruleId);

    /**
     * Get wrapper rule model by ID.
     *
     * @param int $ruleId
     * @return \MageWorx\RewardPoints\Model\Rule
     * @throws \Magento\Framework\Exception\NoSuchEntityException If $id is not found
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getRuleById($ruleId);

    /**
     * Retrieve reward rules that match te specified criteria.
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \MageWorx\RewardPoints\Api\Data\RuleSearchResultInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);

    /**
     * Delete rule by ID.
     *
     * @param int $ruleId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($ruleId);
}
