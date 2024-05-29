<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace MageWorx\CountdownTimersBase\Helper;

use Magento\Framework\Stdlib\DateTime;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;

class TimeStamp
{
    /**
     * @var TimezoneInterface
     */
    private $timezone;

    /**
     * TimeStamp constructor.
     *
     * @param TimezoneInterface $timezone
     */
    public function __construct(TimezoneInterface $timezone)
    {
        $this->timezone = $timezone;
    }

    /**
     * @param int $timeStamp
     * @return int
     */
    public function getLocalTimeStamp($timeStamp): int
    {
        $date      = $this->timezone->date($timeStamp, null, false);
        $date      = $date->format(DateTime::DATETIME_PHP_FORMAT);
        $dateParts = date_parse($date);

        $localDate = $this->timezone->date();
        $localDate
            ->setDate($dateParts['year'], $dateParts['month'], $dateParts['day'])
            ->setTime($dateParts['hour'], $dateParts['minute'], $dateParts['second']);

        return $localDate->getTimestamp();
    }
}
