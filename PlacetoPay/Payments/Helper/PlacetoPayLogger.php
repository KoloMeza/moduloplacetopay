<?php

namespace PlacetoPay\Payments\Helper;

use Magento\Framework\Serialize\Serializer\Json;
use PlacetoPay\Payments\Logger\Logger;

class PlacetoPayLogger
{
    /**
     * @var Logger
     */
    protected $_logger;

    /**
     * @var Json
     */
    protected $_json;

    public function __construct(Logger $logger, Json $json)
    {
        $this->_logger = $logger;
        $this->_json = $json;
    }
    public function log(object $class, string $type, string $message, array $context = [])
    {
        $this->_logger->$type(get_class($class) . ' - ' . $message . ' ' . $this->_json->serialize($context));
    }
}
