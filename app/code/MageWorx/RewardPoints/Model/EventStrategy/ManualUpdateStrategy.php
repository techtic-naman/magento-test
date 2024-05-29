<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\RewardPoints\Model\EventStrategy;

class ManualUpdateStrategy extends \MageWorx\RewardPoints\Model\AbstractEventStrategy
{
    /**
     * @var \Magento\Backend\Model\Auth\Session
     */
    protected $authSession;

    /**
     * @var \Magento\Framework\App\State
     */
    protected $state;

    /**
     * @var \MageWorx\RewardPoints\Helper\ExpirationDate
     */
    protected $helperExpirationDate;

    /**
     * ManualUpdateStrategy constructor.
     *
     * @param \MageWorx\RewardPoints\Helper\Data $helperData
     * @param \Magento\Backend\Model\Auth\Session $authSession
     * @param \MageWorx\RewardPoints\Helper\ExpirationDate $helperExpirationDate
     * @param \Magento\Framework\App\State $state
     * @param array $data
     */
    public function __construct(
        \MageWorx\RewardPoints\Helper\Data $helperData,
        \Magento\Backend\Model\Auth\Session $authSession,
        \MageWorx\RewardPoints\Helper\ExpirationDate $helperExpirationDate,
        \Magento\Framework\App\State $state,
        array $data = []
    ) {
        $this->helperExpirationDate = $helperExpirationDate;
        $this->state                = $state;
        $this->authSession          = $authSession;
        parent::__construct($helperData, $data);
    }

    /**
     * @param array $eventData
     * @param string|null $comment
     * @return \Magento\Framework\Phrase|string
     */
    public function getMessage(array $eventData, $comment = null)
    {
        if ($this->helperData->getUseCommentWrapper()) {

            $name = (!empty($eventData['admin']['name'])) ? $eventData['admin']['name'] : '';

            if ($name && $this->state->getAreaCode() == 'adminhtml') {
                $message = __('Updated by admin %1', '(' . $name . ')');
            } else {
                $message = __('Updated by admin.');
            }

            if ($comment) {
                $message .= ' ' . __('Comment: "%1"', $comment);
            }
        } else {
            $message = $comment;
        }

        return $message;
    }

    /**
     * @return array
     */
    public function getEventData()
    {
        $eventData = [];

        $user = $this->authSession->getUser();

        if ($user) {
            $eventData['admin']['id']   = $user->getId();
            $eventData['admin']['name'] = $user->getName();
        }

        $comment = $this->getEntity()->getData('comment');

        if ($comment) {
            $eventData['comment'] = $comment;
        }

        return $eventData;
    }

    /**
     * @return double|null
     */
    public function getPoints()
    {
        return $this->getEntity()->getData('points_delta');
    }

    /**
     * @return \Magento\User\Model\User|null
     */
    protected function getCurrentUser()
    {
        return $this->authSession->getUser();
    }

    /**
     * @return bool
     */
    public function getIsPossibleSendNotification()
    {
        return (bool)$this->getEntity()->getData('is_need_send_notification');
    }

    /**
     * @return string
     */
    public function getEmailTemplateId()
    {
        $storeId = (int)$this->getEntity()->getData('store_id');

        return $this->helperData->getAdminEmailTemplate($storeId);
    }

    /**
     * @return int|false|null null - expiration period won't be changed, false - expiration period is infinity
     */
    public function getExpirationPeriod()
    {
        if ($this->helperData->isEnableExpirationDate()) {

            $data = $this->getEntity()->getData();

            if (\array_key_exists('expiration_period', $data)) {
                return $data['expiration_period'];
            }

            return null;
        }

        return false;
    }
}