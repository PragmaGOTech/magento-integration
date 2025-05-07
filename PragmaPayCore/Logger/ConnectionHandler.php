<?php
declare(strict_types=1);

namespace Pragma\PragmaPayCore\Logger;

use Magento\Framework\Logger\Handler\Base;
use Monolog\Logger;

class ConnectionHandler extends Base
{
    protected $loggerType = Logger::NOTICE;
    protected $fileName = '/var/log/pragma/connection.log';
}
