<?php

namespace PlacetoPay\Payments\Helper;

use PlacetoPay\Payments\Logger\Logger as LoggerInterface;

class DebugLogger
{
    /**
     * @var string
     */
    protected $uuid;

    /**
     * @var LoggerInterface
     */
    private $logger;
    public function __construct(string $uuid, LoggerInterface $logger)
    {
        $this->uuid = $uuid;
        $this->logger = $logger;
    }

    public function logInfo(string $message, array $data = [])
    {
        $this->logger->info($this->uuid . '-' . $message, $data );
    }

    public function logWarning(string $message, array $data = [])
    {
        $this->logger->warning($this->uuid . '-' . $message, $data);
    }
}