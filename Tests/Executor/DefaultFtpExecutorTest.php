<?php

/**
 * @package    3slab/VdmLibraryFtpTransportBundle
 * @copyright  2020 Suez Smart Solutions 3S.lab
 * @license    https://github.com/3slab/VdmLibraryFtpTransportBundle/blob/master/LICENSE
 */

namespace Vdm\Bundle\LibraryFtpTransportBundle\Tests\Executor;

use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Envelope;
use Vdm\Bundle\LibraryBundle\Stamp\StopAfterHandleStamp;
use Vdm\Bundle\LibraryFtpTransportBundle\Client\FtpClient;
use Vdm\Bundle\LibraryFtpTransportBundle\Executor\DefaultFtpExecutor;

class DefaultFtpExecutorTest extends TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject $logger
     */
    private $logger;

    /**
     * @var FtpClient $ftpClient
     */
    private $ftpClient;

    /**
     * @var DefaultFtpExecutor $ftpExecutor
     */
    private $ftpExecutor;

    protected function setUp(): void
    {
        $this->logger = $this->getMockBuilder(LoggerInterface::class)->getMock();
        $this->ftpClient = $this->getMockBuilder(FtpClient::class)->disableOriginalConstructor()->getMock();
        $this->ftpExecutor = new DefaultFtpExecutor();
        $this->ftpExecutor->setFtpClient($this->ftpClient);
    }

    public function testExecuteWithFiles()
    {
        $files = [
            0 => [
                "path" => "PFE/SAS01/[SpecifToulouse]_[V_Contrat].csv",
                "timestamp" => 1586341946,
                "type" => "file",
                "visibility" => "public",
                "size" => 1492,
                "dirname" => "PFE/SAS01",
                "basename" => "[SpecifToulouse]_[V_Contrat].csv",
                "extension" => "csv",
                "filename" => "[SpecifToulouse]_[V_Contrat]"
            ],
            1 => [
                "path" => "PFE/SAS01/[SpecifToulouse]_[V_Contrat].csv",
                "timestamp" => 1586341946,
                "type" => "file",
                "visibility" => "public",
                "size" => 1492,
                "dirname" => "PFE/SAS01",
                "basename" => "[SpecifToulouse]_[V_Contrat].csv",
                "extension" => "csv",
                "filename" => "[SpecifToulouse]_[V_Contrat]"
            ],
        ];

        $iterator = $this->ftpExecutor->execute($files);

        $this->assertInstanceOf(Envelope::class, $iterator->current());
    }

    public function testExecuteWithFilesLastKey()
    {
        $files = [
            0 => [
                "path" => "PFE/SAS01/[SpecifToulouse]_[V_Contrat].csv",
                "timestamp" => 1586341946,
                "type" => "file",
                "visibility" => "public",
                "size" => 1492,
                "dirname" => "PFE/SAS01",
                "basename" => "[SpecifToulouse]_[V_Contrat].csv",
                "extension" => "csv",
                "filename" => "[SpecifToulouse]_[V_Contrat]"
            ]
        ];

        $iterator = $this->ftpExecutor->execute($files);
        $stamps = $iterator->current()->all();

        $this->assertInstanceOf(Envelope::class, $iterator->current());
        $this->assertArrayHasKey(StopAfterHandleStamp::class, $stamps);
    }

    public function testExecuteWithoutFiles()
    {
        $files = [];

        $iterator = $this->ftpExecutor->execute($files);
        $message = $iterator->current()->getMessage();
        $stamps = $iterator->current()->all();

        $this->assertInstanceOf(Envelope::class, $iterator->current());
        $this->assertEquals("", $message->getPayload());
        $this->assertArrayHasKey(StopAfterHandleStamp::class, $stamps);
    }
}
