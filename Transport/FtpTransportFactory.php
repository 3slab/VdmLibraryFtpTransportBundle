<?php

/**
 * @package    3slab/VdmLibraryFtpTransportBundle
 * @copyright  2020 Suez Smart Solutions 3S.lab
 * @license    https://github.com/3slab/VdmLibraryFtpTransportBundle/blob/master/LICENSE
 */

namespace Vdm\Bundle\LibraryFtpTransportBundle\Transport;

use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Transport\Serialization\SerializerInterface;
use Symfony\Component\Messenger\Transport\TransportFactoryInterface;
use Symfony\Component\Messenger\Transport\TransportInterface;
use Vdm\Bundle\LibraryFtpTransportBundle\Executor\AbstractFtpExecutor;
use Vdm\Bundle\LibraryFtpTransportBundle\Client\Behavior\FtpClientBehaviorFactoryRegistry;
use Vdm\Bundle\LibraryFtpTransportBundle\Client\FtpClientFactoryInterface;
use Vdm\Bundle\LibraryFtpTransportBundle\Client\FtpClientInterface;

class FtpTransportFactory implements TransportFactoryInterface
{
    private const DSN_PROTOCOL_FTP = 'ftp://';
    private const DSN_PROTOCOL_SFTP = 'sftp://';

    private const DSN_PROTOCOLS = [
        self::DSN_PROTOCOL_FTP,
        self::DSN_PROTOCOL_SFTP
    ];

    /**
     * @var LoggerInterface $logger
     */
    private $logger;

    /**
     * @var FtpClientFactoryInterface $ftpClientFactory
     */
    private $ftpClientFactory;

    /**
     * @var FtpClientInterface $ftpClient
     */
    private $ftpClient;

    /**
     * @var AbstractFtpExecutor $ftpExecutor
     */
    private $ftpExecutor;

    /**
     * @var FtpClientBehaviorFactoryRegistry $ftpClientBehaviorFactoryRegistry
     */
    private $ftpClientBehaviorFactoryRegistry;

    public function __construct(
        LoggerInterface $logger, 
        FtpClientFactoryInterface $ftpClientFactory,
        AbstractFtpExecutor $ftpExecutor,
        FtpClientBehaviorFactoryRegistry $ftpClientBehaviorFactoryRegistry
    )
    {
        $this->logger = $logger;
        $this->ftpClientFactory = $ftpClientFactory;
        $this->ftpExecutor = $ftpExecutor;
        $this->ftpClientBehaviorFactoryRegistry = $ftpClientBehaviorFactoryRegistry;
    }

    public function createTransport(string $dsn, array $options, SerializerInterface $serializer): TransportInterface
    {
        $mode = $options['mode'] ?? "read";
        $ftp_options = $options['ftp_options'];

        if (!isset($ftp_options['dirpath'])) {
            throw new \InvalidArgumentException('ftp_options.dirpath is not defined');
        }

        if ($mode === 'move' && !isset($ftp_options['storage'])) {
            throw new \InvalidArgumentException('With mode "move", storage ftp_options has to defined');
        }

        $this->ftpClient = $this->ftpClientFactory->create($dsn, $options);

        $this->ftpClient = $this->ftpClientBehaviorFactoryRegistry->create($this->ftpClient, $options);

        $this->ftpExecutor->setFtpClient($this->ftpClient);

        return new FtpTransport($this->logger, $this->ftpExecutor, $dsn, $mode, $ftp_options);
    }

    public function supports(string $dsn, array $options): bool
    {
        foreach (self::DSN_PROTOCOLS as $protocol) {
            if (0 === strpos($dsn, $protocol)) {
                return true;
            }
        }
        return false;
    }
}
