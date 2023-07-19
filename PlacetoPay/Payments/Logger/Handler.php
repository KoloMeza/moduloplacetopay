<?php

namespace PlacetoPay\Payments\Logger;

use Exception;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Filesystem\DriverInterface;
use Magento\Framework\Logger\Handler\Base;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Monolog\Logger;

/**
 * Class Handler.
 */
class Handler extends Base
{
    /**
     * Logging level.
     *
     * @var int
     */
    protected $loggerType = Logger::DEBUG;

    /**
     * File name.
     *
     * @var string
     */
    protected $fileName = '';

    /**
     * File path.
     *
     * @var string
     */
    public $filePath;

    /**
     * @var TimezoneInterface
     */
    protected $_localeDate;

    /**
     * Handler constructor.
     *
     * @param DriverInterface   $filesystem
     * @param TimezoneInterface $localeDate
     * @param DirectoryList     $dir
     *
     * @throws FileSystemException
     * @throws Exception
     */
    public function __construct(
        DriverInterface $filesystem,
        TimezoneInterface $localeDate,
        DirectoryList $dir
    ) {
        $this->_localeDate = $localeDate;

        $fileName = 'placetopay-' . $this->getTimeStamp() . '.log';
        $ds = DIRECTORY_SEPARATOR;
        $this->filePath = $dir->getPath('log') . $ds . $fileName;

        parent::__construct($filesystem, $this->filePath);
    }

    /**
     * @return string
     */
    public function getTimeStamp()
    {
        return $this->_localeDate->formatDateTime(
            $this->_localeDate->date(),
            null,
            null,
            null,
            null,
            'Y-M-d'
        );
    }

    /**
     * @return bool
     */
    public function exists()
    {
        return file_exists($this->filePath);
    }
}
