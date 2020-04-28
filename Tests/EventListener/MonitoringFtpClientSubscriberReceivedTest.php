<?php

/**
 * @package    3slab/VdmLibraryFtpTransportBundle
 * @copyright  2020 Suez Smart Solutions 3S.lab
 * @license    https://github.com/3slab/VdmLibraryFtpTransportBundle/blob/master/LICENSE
 */

namespace Vdm\Bundle\LibraryFtpTransportBundle\Tests\EventListener;

use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;
use Symfony\Contracts\HttpClient\ResponseInterface;
use Vdm\Bundle\LibraryFtpTransportBundle\Client\Event\FtpClientErrorEvent;
use Vdm\Bundle\LibraryFtpTransportBundle\Client\Event\FtpClientReceivedEvent;
use Vdm\Bundle\LibraryFtpTransportBundle\EventListener\MonitoringFtpClientSubscriber;
use Vdm\Bundle\LibraryBundle\Monitoring\StatsStorageInterface;

class MonitoringFtpClientSubscriberReceivedTest extends TestCase
{
    /**
     * @var StatsStorageInterface $statsStorageInterface
     */
    private $statsStorageInterface;

    protected function getSubscriber(array $calls)
    {
        $this->statsStorageInterface = $this->getMockBuilder(StatsStorageInterface::class)->getMock();
        $this->responseInterface = $this->getMockBuilder(ResponseInterface::class)->getMock();
        $this->statsStorageInterface->expects($calls['sendStat'])->method('sendStat');

        return new MonitoringFtpClientSubscriber($this->statsStorageInterface, new NullLogger());
    }

    /**
     * @dataProvider dataProviderTestOnFtpClientReceivedEvent
     */
    public function testOnFtpClientReceivedEvent($methodCall)
    {
        $listener = $this->getSubscriber(['sendStat' => $methodCall]);

        $file = [
            "size" => 2000,
        ];
        $event = new FtpClientReceivedEvent($file);

        $listener->onFtpClientReceivedEvent($event);
    }

    public function dataProviderTestOnFtpClientReceivedEvent()
    {
        yield [
            $this->once()
        ];
    }
}
