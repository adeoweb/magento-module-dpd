<?php

namespace AdeoWeb\Dpd\Logger\Handler;

use Magento\Framework\Logger\Handler\Base;
use Monolog\Logger;

/**
 * @codeCoverageIgnore
 */
class Dpd extends Base
{
    /**
     * @var string
     */
    protected $fileName = '/var/log/adeoweb_dpd_api.log';

    /**
     * @var int
     */
    protected $loggerType = Logger::DEBUG;
}
