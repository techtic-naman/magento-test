<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\RewardPoints\Model\EventStrategy;

class UnknownEventStrategy extends \MageWorx\RewardPoints\Model\AbstractEventStrategy
{
    /**
     * @var string
     */
    protected $eventCode = 'unknown';

    /**
     * UnknownEventStrategy constructor.
     *
     * @param \MageWorx\RewardPoints\Helper\Data $helperData
     * @param \Magento\Backend\Model\Auth\Session $authSession
     * @param array $data
     */
    public function __construct(
        \MageWorx\RewardPoints\Helper\Data $helperData,
        \Magento\Backend\Model\Auth\Session $authSession,
        array $data = []
    ) {
        if (!empty($data['event_code'])) {
            $this->eventCode = $data['event_code'];
        }
        $this->authSession = $authSession;
        parent::__construct($helperData, $data);
    }

    /**
     * @param array $eventData
     * @param string|null $comment
     * @return \Magento\Framework\Phrase|string
     */
    public function getMessage(array $eventData, $comment = null)
    {
        return __(
            'Unknown event code "%1". Sorry, the message is not available now.',
            htmlspecialchars($this->eventCode)
        );
    }

    /**
     * @return array
     */
    public function getEventData()
    {
        throw new \UnexpectedValueException($this->getErrorMessage(__FUNCTION__));
    }

    /**
     * @return double|null
     */
    public function getPoints()
    {
        throw new \UnexpectedValueException($this->getErrorMessage(__FUNCTION__));
    }

    /**
     * @return bool
     */
    public function getIsPossibleSendNotification()
    {
        throw new \UnexpectedValueException($this->getErrorMessage(__FUNCTION__));
    }

    /**
     * @return string
     */
    public function getEmailTemplateId()
    {
        throw new \UnexpectedValueException($this->getErrorMessage(__FUNCTION__));
    }

    /**
     * @return \Magento\Framework\Phrase
     */
    protected function getErrorMessage($callMethodName)
    {
        return __(
            'MageWorx_RewardPoints: Unknown event code: "%1". Call method name: "%2"',
            htmlspecialchars($this->eventCode),
            $callMethodName
        );
    }
}