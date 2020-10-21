<?php

/**
 * @package    3slab/VdmLibraryFtpTransportBundle
 * @copyright  2020 Suez Smart Solutions 3S.lab
 * @license    https://github.com/3slab/VdmLibraryFtpTransportBundle/blob/master/LICENSE
 */

namespace Vdm\Bundle\LibraryFtpTransportBundle\Transport;

use League\Flysystem\FileExistsException;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Transport\TransportInterface;
use Vdm\Bundle\LibraryFtpTransportBundle\Executor\AbstractFtpExecutor;

class FtpTransport implements TransportInterface
{
    /** 
     * @var LoggerInterface $logger
    */
    private $logger;

    /** 
     * @var AbstractFtpExecutor $ftpExecutor
    */
    private $ftpExecutor;

    /** 
     * @var string $dsn
    */
    private $dsn;

    /** 
     * @var string $mode
    */
    private $mode;

    /** 
     * @var array $options
    */
    private $options;

    public function __construct(
        LoggerInterface $logger,
        AbstractFtpExecutor $ftpExecutor, 
        string $dsn, 
        string $mode,
        array $options
    )
    {
        $this->logger = $logger;
        $this->ftpExecutor = $ftpExecutor;
        $this->dsn = $dsn;
        $this->mode = $mode;
        $this->options = $options;
    }

    public function get(): iterable
    {
        $this->logger->debug('get called');

        $files = $this->ftpExecutor->getFtpClient()->list($this->options['dirpath']);

        return $this->ftpExecutor->execute($files);
    }

    public function ack(Envelope $envelope): void
    {
        $this->logger->debug('ack called');
        $filesystem = $this->ftpExecutor->getFtpClient()->getFilesystem();
        $data = $envelope->getMessage()->getPayload();
        
        switch ($this->mode) {
            case 'read':
                $this->logger->info(sprintf('Use mode read'));
            case 'move':    
                try {
                    $filesystem->copy($data['path'], $this->options['storage'].'/'.$data['basename']);
                    $filesystem->delete($data['path']);
                    $this->logger->info(sprintf('Move file %s to folder %s', $data['basename'], $this->options['storage']));
                } catch (FileExistsException $exception) {
                    // Que faire si le fichier existe déjà ?
                    throw $exception;
                }
            break;
            case 'delete':
                $filesystem->delete($data['path']);
                $this->logger->info(sprintf('Delete file %s', $data['basename']));
            break;
        }
    }

    /**
     * @codeCoverageIgnore
     */
    public function reject(Envelope $envelope): void
    {        
        $this->logger->debug('reject called');
    }

    public function send(Envelope $envelope): Envelope
    {
        $this->logger->debug('send called');

        throw new \Exception('This transport does not support the send action');
    }
}
