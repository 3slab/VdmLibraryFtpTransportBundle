<?php

/**
 * @package    3slab/VdmLibraryFtpTransportBundle
 * @copyright  2020 Suez Smart Solutions 3S.lab
 * @license    https://github.com/3slab/VdmLibraryFtpTransportBundle/blob/master/LICENSE
 */

namespace Vdm\Bundle\LibraryFtpTransportBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Vdm\Bundle\LibraryFtpTransportBundle\Client\Behavior\FtpClientBehaviorFactoryRegistry;

class FtpClientBehaviorCreateCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->has(FtpClientBehaviorFactoryRegistry::class)) {
            return;
        }

        $definition = $container->findDefinition(FtpClientBehaviorFactoryRegistry::class);
        $taggedServices = $container->findTaggedServiceIds('vdm_library.ftp_decorator_factory');

        foreach ($taggedServices as $id => $tags) {
            $definition->addMethodCall('addFactory', [new Reference($id), $id::priority()]);
        }
    }
}
