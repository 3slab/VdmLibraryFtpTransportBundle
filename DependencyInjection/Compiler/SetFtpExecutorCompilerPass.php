<?php

/**
 * @package    3slab/VdmLibraryFtpTransportBundle
 * @copyright  2020 Suez Smart Solutions 3S.lab
 * @license    https://github.com/3slab/VdmLibraryFtpTransportBundle/blob/master/LICENSE
 */

namespace Vdm\Bundle\LibraryFtpTransportBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Vdm\Bundle\LibraryFtpTransportBundle\Executor\AbstractFtpExecutor;
use Vdm\Bundle\LibraryFtpTransportBundle\Executor\DefaultFtpExecutor;
use Vdm\Bundle\LibraryFtpTransportBundle\Transport\FtpTransportFactory;

class SetFtpExecutorCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->has(FtpTransportFactory::class)) {
            return;
        }

        $taggedServicesFtpExecutor = $container->findTaggedServiceIds('vdm_library.ftp_executor');

        // Unload default ftp executor if multiple ftpExecutor
        if (count($taggedServicesFtpExecutor) > 1) {
            foreach ($taggedServicesFtpExecutor as $id => $tags) {
                if ($id === DefaultFtpExecutor::class) {
                    unset($taggedServicesFtpExecutor[$id]);
                    break;
                }
            }
        }

        $container->setAlias(AbstractFtpExecutor::class, array_key_first($taggedServicesFtpExecutor));
    }
}
