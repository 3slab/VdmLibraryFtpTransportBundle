<?php

/**
 * @package    3slab/VdmLibraryFtpTransportBundle
 * @copyright  2020 Suez Smart Solutions 3S.lab
 * @license    https://github.com/3slab/VdmLibraryFtpTransportBundle/blob/master/LICENSE
 */

namespace Vdm\Bundle\LibraryFtpTransportBundle\Transport;

use League\Flysystem\FileExistsException;
use League\Flysystem\FilesystemInterface;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Envelope;
use Vdm\Bundle\LibraryFtpTransportBundle\Client\FtpClientInterface;
use Vdm\Bundle\LibraryBundle\Model\Message;
use Vdm\Bundle\LibraryFtpTransportBundle\Client\FtpClient;
use Vdm\Bundle\LibraryFtpTransportBundle\Executor\DefaultFtpExecutor;
use Vdm\Bundle\LibraryFtpTransportBundle\Transport\FtpTransport;

class FtpTransportTest extends TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject $logger
     */
    private $logger;

    /**
     * @var FtpTransport $ftpTransport
     */
    private $ftpTransport;

    protected function setUp(): void
    {
        $this->logger = $this->getMockBuilder(LoggerInterface::class)->getMock();
        $this->ftpClient = $this->getMockBuilder(FtpClient::class)->disableOriginalConstructor()->setMethods(['list'])->getMock();
        $this->ftpExecutor = $this
                        ->getMockBuilder(DefaultFtpExecutor::class)
                        ->setMethods(null)
                        ->getMock();
        $this->ftpExecutor->setFtpClient($this->ftpClient);
        $this->ftpTransport = $this
                        ->getMockBuilder(FtpTransport::class)
                        ->setConstructorArgs([$this->logger, $this->ftpExecutor, "sftp://localhost:22", "move", ['dirpath' => 'PFE/SAS01/']])
                        ->setMethods(null)
                        ->getMock();
    }

    public function testGet()
    {
        $this->ftpClient->method('list')->willReturn([]);
        $iterator = $this->ftpTransport->get();
        
        $this->assertInstanceOf(Envelope::class, $iterator->current());
    }

    public function testAckMove()
    {
        $message = [
            "path" => "PFE/SAS01/[SpecifToulouse]_[V_Contrat].csv",
            "timestamp" => 1586341946,
            "type" => "file",
            "visibility" => "public",
            "size" => 1492,
            "dirname" => "PFE/SAS01",
            "basename" => "[SpecifToulouse]_[V_Contrat].csv",
            "extension" => "csv",
            "filename" => "[SpecifToulouse]_[V_Contrat]"
        ];
        $filesystem = $this->getMockBuilder(FilesystemInterface::class)->getMock();
        $ftpClient = $this->getMockBuilder(FtpClientInterface::class)->getMock();
        $ftpExecutor = $this
                ->getMockBuilder(DefaultFtpExecutor::class)
                ->setMethods(['getFtpClient'])
                ->getMock();
        $ftpTransport = $this
                ->getMockBuilder(FtpTransport::class)
                ->setConstructorArgs([$this->logger, $ftpExecutor, "sftp://localhost:22", "move", ['storage' => 'PFE/SAS01/Storage']])
                ->setMethods(null)
                ->getMock();
        $ftpExecutor->expects($this->once())->method('getFtpClient')->willReturn($ftpClient);
        $ftpClient->expects($this->once())->method('getFilesystem')->willReturn($filesystem);
        $filesystem->expects($this->once())->method('copy');
        $filesystem->expects($this->once())->method('delete');
        $envelope = new Envelope(new Message($message));
        $ftpTransport->ack($envelope);
    }

    public function testAckMoveException()
    {
        $message = [
            "path" => "PFE/SAS01/[SpecifToulouse]_[V_Contrat].csv",
            "timestamp" => 1586341946,
            "type" => "file",
            "visibility" => "public",
            "size" => 1492,
            "dirname" => "PFE/SAS01",
            "basename" => "[SpecifToulouse]_[V_Contrat].csv",
            "extension" => "csv",
            "filename" => "[SpecifToulouse]_[V_Contrat]"
        ];
        $filesystem = $this->getMockBuilder(FilesystemInterface::class)->getMock();
        $ftpClient = $this->getMockBuilder(FtpClientInterface::class)->getMock();
        $ftpExecutor = $this
                ->getMockBuilder(DefaultFtpExecutor::class)
                ->setMethods(['getFtpClient'])
                ->getMock();
        $ftpTransport = $this
                ->getMockBuilder(FtpTransport::class)
                ->setConstructorArgs([$this->logger, $ftpExecutor, "sftp://localhost:22", "move", ['storage' => 'PFE/SAS01/Storage']])
                ->setMethods(null)
                ->getMock();
        $ftpExecutor->expects($this->once())->method('getFtpClient')->willReturn($ftpClient);
        $ftpClient->expects($this->once())->method('getFilesystem')->willReturn($filesystem);
        $filesystem->expects($this->once())->method('copy')->willThrowException(new FileExistsException(''));
        $filesystem->expects($this->never())->method('delete');
        $this->expectException(FileExistsException::class);
        $envelope = new Envelope(new Message($message));
        $ftpTransport->ack($envelope);
    }

    public function testAckDelete()
    {
        $message = [
            "path" => "PFE/SAS01/[SpecifToulouse]_[V_Contrat].csv",
            "timestamp" => 1586341946,
            "type" => "file",
            "visibility" => "public",
            "size" => 1492,
            "dirname" => "PFE/SAS01",
            "basename" => "[SpecifToulouse]_[V_Contrat].csv",
            "extension" => "csv",
            "filename" => "[SpecifToulouse]_[V_Contrat]"
        ];
        $filesystem = $this->getMockBuilder(FilesystemInterface::class)->getMock();
        $ftpClient = $this->getMockBuilder(FtpClientInterface::class)->getMock();
        $ftpExecutor = $this
                ->getMockBuilder(DefaultFtpExecutor::class)
                ->setMethods(['getFtpClient'])
                ->getMock();
        $ftpTransport = $this
                ->getMockBuilder(FtpTransport::class)
                ->setConstructorArgs([$this->logger, $ftpExecutor, "sftp://localhost:22", "delete", []])
                ->setMethods(null)
                ->getMock();
        $ftpExecutor->expects($this->once())->method('getFtpClient')->willReturn($ftpClient);
        $ftpClient->expects($this->once())->method('getFilesystem')->willReturn($filesystem);
        $filesystem->expects($this->never())->method('copy');
        $filesystem->expects($this->once())->method('delete');
        $envelope = new Envelope(new Message($message));
        $ftpTransport->ack($envelope);
    }

    public function testSend()
    {
        $ftpTransport = $this
                ->getMockBuilder(FtpTransport::class)
                ->setConstructorArgs([$this->logger, $this->ftpExecutor, "sftp://localhost:22", "delete", []])
                ->setMethods(null)
                ->getMock();

        $this->expectException(\Exception::class);

        $envelope = new Envelope(new Message(""));
        $ftpTransport->send($envelope);        
    }
}
