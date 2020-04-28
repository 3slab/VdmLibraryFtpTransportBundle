<?php

/**
 * @package    3slab/VdmLibraryFtpTransportBundle
 * @copyright  2020 Suez Smart Solutions 3S.lab
 * @license    https://github.com/3slab/VdmLibraryFtpTransportBundle/blob/master/LICENSE
 */

namespace Vdm\Bundle\LibraryFtpTransportBundle\Client\Behavior;

use Psr\Log\LoggerInterface;
use Vdm\Bundle\LibraryFtpTransportBundle\Client\FtpClientInterface;

interface FtpClientBehaviorFactoryInterface
{
    public static function priority(int $priority = 0);

    public function createDecoratedFtpClient(LoggerInterface $logger, FtpClientInterface $ftpClient, array $options);

    public function support(array $options);
}
