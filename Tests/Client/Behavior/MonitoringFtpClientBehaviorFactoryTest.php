<?php

/**
 * @package    3slab/VdmLibraryFtpTransportBundle
 * @copyright  2020 Suez Smart Solutions 3S.lab
 * @license    https://github.com/3slab/VdmLibraryFtpTransportBundle/blob/master/LICENSE
 */

namespace Vdm\Bundle\LibraryFtpTransportBundle\Tests\Client\Behavior;

use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Vdm\Bundle\LibraryFtpTransportBundle\Client\FtpClientInterface;
use Vdm\Bundle\LibraryFtpTransportBundle\Client\MonitoringFtpClientBehavior;
use Vdm\Bundle\LibraryFtpTransportBundle\Client\Behavior\MonitoringFtpClientBehaviorFactory;

class MonitoringFtpClientBehaviorFactoryTest extends TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject $logger
     */
    private $logger;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject $ftpClient
     */
    private $ftpClient;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject $eventDispatcher
     */
    private $eventDispatcher;

    /**
     * @var MonitoringFtpClientBehavior $monitoringFtpClient
     */
    private $monitoringFtpClient;

    protected function setUp(): void
    {
        $this->logger = $this->getMockBuilder(LoggerInterface::class)->getMock();
        $this->ftpClient = $this->getMockBuilder(FtpClientInterface::class)->getMock();
        $this->eventDispatcher = $this->getMockBuilder(EventDispatcherInterface::class)->getMock();

        $this->monitoringFtpClient = new MonitoringFtpClientBehaviorFactory($this->eventDispatcher);
    }

    public function testPriority()
    {
        $monitoring = MonitoringFtpClientBehaviorFactory::priority(5);

        $this->assertEquals(5, $monitoring);
    }
    
    public function testCreateDecoratedFtpClient()
    {
        $monitoringFtpClient = $this->monitoringFtpClient->createDecoratedFtpClient($this->logger, $this->ftpClient, []);
        
        $this->assertInstanceOf(MonitoringFtpClientBehavior::class, $monitoringFtpClient);
    }

    public function testSupport()
    {
        $options["monitoring"] = [
            "enabled" => true
        ];
        $result = $this->monitoringFtpClient->support($options);

        $this->assertTrue($result);
    }

    public function testNotSupport()
    {
        $options["monitoring"] = [
            "enabled" => false
        ];
        $result = $this->monitoringFtpClient->support($options);

        $this->assertFalse($result);
    }
}
