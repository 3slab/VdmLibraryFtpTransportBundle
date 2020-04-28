<?php

/**
 * @package    3slab/VdmLibraryFtpTransportBundle
 * @copyright  2020 Suez Smart Solutions 3S.lab
 * @license    https://github.com/3slab/VdmLibraryFtpTransportBundle/blob/master/LICENSE
 */

namespace Vdm\Bundle\LibraryFtpTransportBundle\EventListener;

use Vdm\Bundle\LibraryBundle\Monitoring\StatsStorageInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Psr\Log\LoggerInterface;
use Vdm\Bundle\LibraryFtpTransportBundle\Client\Event\FtpClientErrorEvent;
use Vdm\Bundle\LibraryFtpTransportBundle\Client\Event\FtpClientReceivedEvent;
use Vdm\Bundle\LibraryFtpTransportBundle\Monitoring\Model\FtpClientErrorStat;
use Vdm\Bundle\LibraryFtpTransportBundle\Monitoring\Model\FtpClientResponseStat;

class MonitoringFtpClientSubscriber implements EventSubscriberInterface
{
    /**
     * @var StatsStorageInterface
     */
    private $storage;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * MonitoringFtpClientSubscriber constructor.
     *
     * @param StatsStorageInterface $storage
     * @param LoggerInterface|null $messengerLogger
     */
    public function __construct(StatsStorageInterface $storage, LoggerInterface $messengerLogger = null)
    {
        $this->storage = $storage;
        $this->logger = $messengerLogger;
    }

    /**
     * Method executed on FtpClientReceivedEvent event
     *
     * @param FtpClientReceivedEvent $event
     */
    public function onFtpClientReceivedEvent(FtpClientReceivedEvent $event)
    {
        $file = $event->getFile();
        $size = $file['size'];
        
        $this->logger->debug(sprintf('size: %s', $size));

        $ftpClientResponseStat = new FtpClientResponseStat($size);
        $this->storage->sendStat($ftpClientResponseStat);
    }

    /**
     * Method executed on FtpClientErrorEvent event
     *
     * @param FtpClientErrorEvent $event
     */
    public function onFtpClientErrorEvent(FtpClientErrorEvent $event)
    {
        $error = $event->getError();
        
        $this->logger->debug(sprintf('error: %d', $error));

        $ftpClienErrorStat = new FtpClientErrorStat($error);
        $this->storage->sendStat($ftpClienErrorStat);
    }

    /**
     * {@inheritDoc}
     * @codeCoverageIgnore
     */
    public static function getSubscribedEvents()
    {
        return [
            FtpClientReceivedEvent::class => 'onFtpClientReceivedEvent',
            FtpClientErrorEvent::class => 'onFtpClientErrorEvent',
        ];
    }
}
