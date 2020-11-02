<?php

/**
 * @package    3slab/VdmLibraryFtpTransportBundle
 * @copyright  2020 Suez Smart Solutions 3S.lab
 * @license    https://github.com/3slab/VdmLibraryFtpTransportBundle/blob/master/LICENSE
 */

namespace Vdm\Bundle\LibraryFtpTransportBundle\Helper;

use League\Flysystem\Filesystem;
use Vdm\Bundle\LibraryFtpTransportBundle\Client\FtpClientFactoryInterface;

class SftpHelper
{
    /**
     * @var Filesystem $filesystem
     */
    protected $filesystem;

    public function __construct($dsn, FtpClientFactoryInterface $ftpClientFactory)
    {
        $this->filesystem = $ftpClientFactory->create($dsn, [])->getFileSystem();
    }

    public function renameFile($path, $newPath)
    {
        return $this->filesystem->rename($path, $newPath);
    }

    public function writeFile($path, $content)
    {
        $this->filesystem->write($path, $content);
    }
}