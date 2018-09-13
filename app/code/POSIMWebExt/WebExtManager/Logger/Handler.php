<?php

namespace POSIMWebExt\WebExtManager\Logger;

use Monolog\Logger;

class Handler extends \Magento\Framework\Logger\Handler\Base
{
    /**
     * log level
     * @var int
     */
    protected $loggerType = Logger::DEBUG;

    /**File name
     * @var string
     */
    protected $fileName = '/var/log/posim_linker.log';
}