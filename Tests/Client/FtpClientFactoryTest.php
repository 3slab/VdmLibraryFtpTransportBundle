<?php

/**
 * @package    3slab/VdmLibraryFtpTransportBundle
 * @copyright  2020 Suez Smart Solutions 3S.lab
 * @license    https://github.com/3slab/VdmLibraryFtpTransportBundle/blob/master/LICENSE
 */

namespace Vdm\Bundle\LibraryFtpTransportBundle\Tests\Client;

use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Vdm\Bundle\LibraryFtpTransportBundle\Client\FtpClientFactory;
use Vdm\Bundle\LibraryFtpTransportBundle\Client\FtpClient;

class FtpClientFactoryTest extends TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject $logger
     */
    private $logger;

    /**
     * @var FtpClientFactory $ftpClientFactory
     */
    private $ftpClientFactory;

    protected function setUp(): void
    {
        $this->logger = $this->getMockBuilder(LoggerInterface::class)->getMock();
        $this->ftpClientFactory = new FtpClientFactory($this->logger);
    }

    public function testCreate()
    {    
        $ftpClient = $this->ftpClientFactory->create("sftp://localhost:22", []);

        $this->assertInstanceOf(FtpClient::class, $ftpClient);
    }
}
