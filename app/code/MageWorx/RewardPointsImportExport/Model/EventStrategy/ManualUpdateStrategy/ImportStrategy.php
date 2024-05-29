<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\RewardPointsImportExport\Model\EventStrategy\ManualUpdateStrategy;

use \MageWorx\RewardPointsImportExport\Model\Balance\CsvImportHandler;

class ImportStrategy extends \MageWorx\RewardPoints\Model\EventStrategy\ManualUpdateStrategy
{
    /**
     * {@inheritdoc}
     */
    public function getMessage(array $eventData, $comment = null)
    {
        $message = '';

        if ($this->helperData->getUseCommentWrapper()) {

            $name = (!empty($eventData['admin']['name'])) ? $eventData['admin']['name'] : '';

            if ($name && $this->state->getAreaCode() == 'adminhtml') {
                $message = __('Updated by admin %1', '(' . $name . ')');
            } else {
                $message = __('Updated by admin.');
            }

            $message .= ' ';
        }

        if (!empty($eventData['reset_comment'])) {
            $message .= $eventData['reset_comment'];
        } else {
            if (!empty($eventData['comment'])) {

                if ($this->helperData->getUseCommentWrapper()) {
                    $message .= __('Comment: "%1"', $comment);
                } else {
                    $message .= $comment;
                }
            }
        }

        return $message;
    }

    /**
     * @return int|false|null null - expiration period won't be changed, false - expiration period is infinity
     */
    public function getExpirationPeriod()
    {
        if ($this->getExpirationPeriodFromConfig() === false) {
            return false;
        }

        $expirationPeriod = $this->getEntity()->getExpirationPeriod();

        if (is_numeric($expirationPeriod)) {
            if ((int)$expirationPeriod === 0) {
                return null;
            } else {
                return (int)$expirationPeriod;
            }
        } else {
            if (!$expirationPeriod || $expirationPeriod == CsvImportHandler::EXPIRATION_PERIOD_UNCHANGED) {
                return null;
            }

            if ($expirationPeriod == CsvImportHandler::EXPIRATION_PERIOD_UNLIMITED) {
                return false;
            }

            if ($expirationPeriod == CsvImportHandler::EXPIRATION_PERIOD_DEFAULT) {
                return $this->getExpirationPeriodFromConfig();
            }
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getEventData()
    {
        $eventData = parent::getEventData();
        $comment   = $this->getEntity()->getData('reset_comment');

        if ($comment) {
            $eventData['reset_comment'] = $comment;
        }

        return $eventData;
    }
}