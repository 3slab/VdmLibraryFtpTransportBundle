<?php

/**
 * @package    3slab/VdmLibraryHttpTransportBundle
 * @copyright  2020 Suez Smart Solutions 3S.lab
 * @license    https://github.com/3slab/VdmLibraryHttpTransportBundle/blob/master/LICENSE
 */

namespace Vdm\Bundle\LibraryFtpTransportBundle;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Vdm\Bundle\LibraryFtpTransportBundle\DependencyInjection\Compiler\FtpClientBehaviorCreateCompilerPass;
use Vdm\Bundle\LibraryFtpTransportBundle\DependencyInjection\Compiler\SetFtpExecutorCompilerPass;

class VdmLibraryFtpTransportBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new SetFtpExecutorCompilerPass());
        $container->addCompilerPass(new FtpClientBehaviorCreateCompilerPass());
    }
}
