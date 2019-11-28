<?php

namespace POSIMWebExt\GCLink\Logger;

class Handler extends \Magento\Framework\Logger\Handler\Base
{
    /**
     * @var string
     */
    protected $fileName = '/var/log/posim_gclink.log';
    /**
     * @var int
     */
    protected $loggerType = Logger::DEBUG;
}
