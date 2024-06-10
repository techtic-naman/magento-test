<?php
/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_Mpmembership
 * @author    Webkul Software Private Limited
 * @copyright Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */
namespace Webkul\Mpmembership\Logger;

class Handler extends \Magento\Framework\Logger\Handler\Base
{
    /**
     * @var int
     */
    protected $loggerType = Logger::INFO;

    /**
     * @var string
     */
    protected $fileName = '/var/log/Webkul_Mpmembership.log';
}
