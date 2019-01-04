<?php

namespace AdeoWeb\Dpd\Logger\Handler;

use Magento\Framework\Logger\Handler\Base;
use Monolog\Logger;

class Dpd extends Base
{
    protected $fileName = '/var/log/adeoweb_dpd_api.log';
    protected $loggerType = Logger::DEBUG;
}