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
use Vdm\Bundle\LibraryFtpTransportBundle\EventListener\MonitoringFtpClientSubscriber;
use Vdm\Bundle\LibraryBundle\Monitoring\StatsStorageInterface;

class MonitoringFtpClientSubscriberErrorTest extends TestCase
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
     * @dataProvider dataProviderTestOnFtpClientErrorEvent
     */
    public function testOnFtpClientErrorEvent($methodCall)
    {
        $listener = $this->getSubscriber(['sendStat' => $methodCall]);

        $event = new FtpClientErrorEvent();

        $listener->onFtpClientErrorEvent($event);
    }

    public function dataProviderTestOnFtpClientErrorEvent()
    {
        yield [
            $this->once()
        ];
    }
}
