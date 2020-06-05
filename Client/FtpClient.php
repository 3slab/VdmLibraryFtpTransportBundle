<?php

/**
 * @package    3slab/VdmLibraryFtpTransportBundle
 * @copyright  2020 Suez Smart Solutions 3S.lab
 * @license    https://github.com/3slab/VdmLibraryFtpTransportBundle/blob/master/LICENSE
 */

namespace Vdm\Bundle\LibraryFtpTransportBundle\Client;

use Exception;
use League\Flysystem\Adapter\Ftp as Adapter;
use League\Flysystem\FileNotFoundException;
use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemInterface;
use League\Flysystem\Sftp\SftpAdapter;
use Psr\Log\LoggerInterface;

class FtpClient implements FtpClientInterface
{
    /**
     * @var LoggerInterface $messengerLogger
     */
    private $logger;

    /**
     * @var Filesystem $filesystem
     */
    private $filesystem;

    public function __construct(
        string $host, 
        int $port, 
        string $user, 
        string $password, 
        bool $sftp, 
        array $options, 
        LoggerInterface $messengerLogger
    ) 
    {
        $this->logger = $messengerLogger;
        if ($sftp) {
            $this->filesystem = $this->filesystem = new Filesystem(new SftpAdapter([
                'host' => $host,
                'port' => $port,
                'username' => $user,
                'password' => $password,
                'privateKey' => (isset($options['privateKey'])) ? $options['privateKey'] : '',
                'root' => (isset($options['root'])) ? $options['root'] : '',
                'timeout' => (isset($options['timeout'])) ? $options['timeout'] : 10,
            ]));
        } else {
            $this->filesystem = new Filesystem(new Adapter([
                'host' => $host,
                'username' => $user,
                'password' => $password,
            
                /** optional config settings */
                'port' => $port,
                'root' => (isset($options['root'])) ? $options['root'] : '',
                'passive' => (isset($options['passive'])) ? $options['passive'] : true,
                'ssl' => (isset($options['ssl'])) ? $options['ssl'] : true,
                'timeout' => (isset($options['timeout'])) ? $options['timeout'] : 30,
                'ignorePassiveAddress' => (isset($options['ignorePassiveAddress'])) ? $options['ignorePassiveAddress'] : false,
            ]));
        }
    }

    /**
     * Get file content
     * 
     * @return array
     * @throws FileNotFoundException
     */
    public function get(array $file): array
    {
        try {
            $file = $this->download($file);
        } catch (Exception $e) {
            // Most of the errors are because of timeout disconnect. it forces reconnect on next operation
            $this->filesystem->getAdapter()->disconnect();
            $file = $this->download($file);
        }

        return $file;
    }

    /**
     * Get file content
     *
     * @return array
     * @throws FileNotFoundException
     */
    public function download($file): array
    {
        $tempnam = tempnam('/tmp', uniqid());
        $file['tmpfilepath'] = $tempnam;

        $this->logger->debug(sprintf('Start downloading the file %s to: %s', $file['basename'], $tempnam));
        $tempFile = fopen($tempnam, 'wb+');
        $stream = $this->filesystem->readStream($file['path']);
        while (!feof($stream)) {
            if (fwrite($tempFile, fread($stream, 8192)) === false) {
                $this->logger->error('Unable to write to temp file');
                break;
            }
        }
        fclose($stream);
        fclose($tempFile);
        $this->logger->debug('Download complete');

        return $file;
    }

    /**
     * Get all files/directories in this directory
     * 
     * @param string $dirpath directory path to list
     * 
     * @return array|null list of files or directories in this path
     */
    public function list(string $dirpath): ?array
    {
        $files = null;
        if ($this->filesystem->has($dirpath)) {
            $files = $this->filesystem->listContents($dirpath);
        } else {
            $this->logger->info(sprintf('Directory %s inexistant sur le serveur', $dirpath));
        }

        return $files;
    }

    /**
     * @return FilesystemInterface
     */
    public function getFileSystem(): FilesystemInterface
    {
        return $this->filesystem;
    }

    /**
     * @param FilesystemInterface $filesystem
     * @return $this
     */
    public function setFileSystem(FilesystemInterface $filesystem): self
    {
        $this->filesystem = $filesystem;

        return $this;
    }
}
