<?php

/**
 * @package    3slab/VdmLibraryFtpTransportBundle
 * @copyright  2020 Suez Smart Solutions 3S.lab
 * @license    https://github.com/3slab/VdmLibraryFtpTransportBundle/blob/master/LICENSE
 */

namespace Vdm\Bundle\LibraryFtpTransportBundle\Executor;

use Vdm\Bundle\LibraryFtpTransportBundle\Client\FtpClientInterface;

abstract class AbstractFtpExecutor
{
    /** 
     * @var FtpClientInterface $ftpClient
    */
    protected $ftpClient;

    public function __construct() {
    }

    abstract public function execute(array $files): iterable;

    public function getFtpClient(): FtpClientInterface
    {
        return $this->ftpClient;
    }

    public function setFtpClient(FtpClientInterface $ftpClient)
    {
        $this->ftpClient = $ftpClient;
    }
}
