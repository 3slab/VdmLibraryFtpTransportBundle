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
use Vdm\Bundle\LibraryFtpTransportBundle\Client\Behavior\FtpClientBehaviorFactoryRegistry;
use Vdm\Bundle\LibraryFtpTransportBundle\Client\Behavior\MonitoringFtpClientBehaviorFactory;
use Vdm\Bundle\LibraryFtpTransportBundle\Client\MonitoringFtpClientBehavior;
use Vdm\Bundle\LibraryFtpTransportBundle\Client\FtpClient;

class FtpClientBehaviorFactoryRegistryTest extends TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject $logger
     */
    private $logger;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject $eventDispatcher
     */
    private $eventDispatcher;

    /**
     * @var FtpClient $ftpClient
     */
    private $ftpClient;

    /**
     * @var FtpClientBehaviorFactoryRegistry $ftpClientBehavior
     */
    private $ftpClientBehavior;

    protected function setUp(): void
    {
        $this->logger = $this->getMockBuilder(LoggerInterface::class)->getMock();
        $this->eventDispatcher = $this->getMockBuilder(EventDispatcherInterface::class)->getMock();
        $this->ftpClient = new FtpClient('localhost', 22, '', '', true, [], $this->logger);

        $this->ftpClientBehavior = new FtpClientBehaviorFactoryRegistry($this->logger);
    }

    public function testAddFactory()
    {
        $monitoringrFtpClientBehaviorFactory = new MonitoringFtpClientBehaviorFactory($this->eventDispatcher);
        $priorityMonitoring = 0;

        $property = new \ReflectionProperty(FtpClientBehaviorFactoryRegistry::class, 'ftpClientBehavior');
        $property->setAccessible(true);
        $value = $property->getValue($this->ftpClientBehavior);
        $this->assertEmpty($value);
        try {
            $this->ftpClientBehavior->addFactory($monitoringrFtpClientBehaviorFactory, $priorityMonitoring);
        } catch (\Exception $exception) {

        }

        $value = $property->getValue($this->ftpClientBehavior);
        $this->assertNotEmpty($value);
        $this->assertCount(1, $value);
    }

    public function testCreate()
    {
        $ftpClient = $this->ftpClientBehavior->create($this->ftpClient, []);

        $this->assertInstanceOf(FtpClient::class, $ftpClient);
    }

    public function testCreateNotSupport()
    {
        $ftpClient = $this->ftpClientBehavior->create($this->ftpClient, []);

        $this->assertInstanceOf(FtpClient::class, $ftpClient);
    }

    public function testCreateSupport()
    {
        $monitoringrFtpClientBehaviorFactory = new MonitoringFtpClientBehaviorFactory($this->eventDispatcher);
        $priorityMonitoring = 0;
        $this->ftpClientBehavior->addFactory($monitoringrFtpClientBehaviorFactory, $priorityMonitoring);
        $ftpClient = $this->ftpClientBehavior->create($this->ftpClient, ['monitoring' => ['enabled' => true]]);

        $this->assertInstanceOf(MonitoringFtpClientBehavior::class, $ftpClient);
    }
}
