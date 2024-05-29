<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\RewardPoints\Model\EventStrategy\RuleStrategy;

class EarnByOrderStrategy extends \MageWorx\RewardPoints\Model\EventStrategy\AbstractRuleStrategy
{
    /**
     * @var \MageWorx\RewardPoints\Helper\Price
     */
    protected $helperPrice;

    /**
     * @var \Magento\Sales\Api\OrderRepositoryInterface
     */
    protected $orderRepository;

    /**
     * @var \Magento\Framework\Serialize\Serializer\Json
     */
    protected $serializer;

    /**
     * @var bool
     */
    protected $strictRuleMode = false;

    /**
     * EarnByOrderStrategy constructor.
     *
     * @param \MageWorx\RewardPoints\Model\ResourceModel\Rule $ruleResource
     * @param \MageWorx\RewardPoints\Helper\Data $helperData
     * @param \MageWorx\RewardPoints\Helper\Price $helperPrice
     * @param \Magento\Sales\Api\OrderRepositoryInterface $orderRepository
     * @param \Magento\Framework\Serialize\Serializer\Json $serializer
     * @param array $data
     */
    public function __construct(
        \MageWorx\RewardPoints\Model\ResourceModel\Rule $ruleResource,
        \MageWorx\RewardPoints\Helper\Data $helperData,
        \MageWorx\RewardPoints\Helper\Price $helperPrice,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        \Magento\Framework\Serialize\Serializer\Json $serializer,
        array $data = []
    ) {
        $this->helperPrice     = $helperPrice;
        $this->orderRepository = $orderRepository;
        $this->serializer = $serializer;
        parent::__construct($ruleResource, $helperData, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function getMessage(array $eventData, $comment = null)
    {
        $message = '';

        if (!empty($eventData['increment_id'])) {
            $message = __('The reward points were added for the completed order %1', $eventData['increment_id']);
        }

        return $message;
    }

    /**
     * {@inheritdoc}
     */
    public function useImmediateSending()
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function processAfterTransactionSave($pointTransaction)
    {
        if ($pointTransaction->getId() && $pointTransaction->getPointsDelta()) {

            if ($this->getRule()) {
                $ruleName = 'ID#' . $this->getRule()->getId();
            } elseif ($this->getRuleId()) {
                $ruleName = 'ID#' . $this->getRuleId() . ' ' . __('(currently not valid or deleted)');
            }

            /** @var \Magento\Sales\Model\Order $order */
            $order = $this->getEntity();

            if ($order instanceof \Magento\Sales\Api\Data\OrderInterface) {

                $order->addStatusHistoryComment(
                    __(
                        '%1 were earned with this order (Rule %2).',
                        $this->helperPrice->getFormattedPoints($pointTransaction->getPointsDelta()),
                        $ruleName
                    )
                );

                $this->orderRepository->save($order);
            }
        }

        return parent::processAfterTransactionSave($pointTransaction);
    }

    /**
     * {@inheritdoc}
     */
    public function getEventData()
    {
        $eventData = parent::getEventData();

        $eventData['increment_id'] = $this->getEntity()->getIncrementId();

        return $eventData;
    }

    /**
     * @return float|null
     */
    protected function loadPointsFromRule()
    {
        $ruleDataAsString = $this->getEntity()->getMwEarnPointsData();

        if ($ruleDataAsString) {
            $ruleData          = $this->serializer->unserialize($ruleDataAsString);
            $this->pointAmount = $ruleData[$this->getRuleId()] ?? 0;
        } else {
            $this->pointAmount = 0;
        }

        return $this->pointAmount;
    }

    /**
     * {@inheritdoc}
     */
    public function getIsPossibleSendNotification()
    {
        if ($this->getRule()) {
            return parent::getIsPossibleSendNotification();
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function getEmailTemplateId()
    {
        if ($this->getRule()) {
            return parent::getEmailTemplateId();
        }

        //There is no send message for this case
        return '0';
    }

    /**
     * @return array
     */
    protected function getRuleEventData()
    {
        if ($this->getRule()) {
            return parent::getRuleEventData();
        }

        if ($this->getRuleId()) {
            return [
                'rule_id' => $this->getRuleId(),
            ];
        }
    }
}