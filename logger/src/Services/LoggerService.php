<?php

namespace Hp\Logger\Services;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class LoggerService
{
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger('ps_logger');
        $this->logger->pushHandler(new StreamHandler(_PS_ROOT_DIR_.'/var/logs/logger.log', Logger::DEBUG));
    }
    public function logInfo($message, array $context = [])
    {
        $this->logger->info($message, $context);
    }

    public function logError($message, array $context = [])
    {
        $this->logger->error($message, $context);
    }
}
