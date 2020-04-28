<?php

/**
 * @package    3slab/VdmLibraryFtpTransportBundle
 * @copyright  2020 Suez Smart Solutions 3S.lab
 * @license    https://github.com/3slab/VdmLibraryFtpTransportBundle/blob/master/LICENSE
 */

namespace Vdm\Bundle\LibraryFtpTransportBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Vdm\Bundle\LibraryFtpTransportBundle\Client\Behavior\FtpClientBehaviorFactoryInterface;
use Vdm\Bundle\LibraryFtpTransportBundle\Executor\AbstractFtpExecutor;

/**
 * Class VdmLibraryExtension
 *
 * @package Vdm\Bundle\LibraryFtpTransportBundle\DependencyInjection
 */
class VdmLibraryFtpTransportExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $container->registerForAutoconfiguration(AbstractFtpExecutor::class)
            ->addTag('vdm_library.ftp_executor')
        ;
        $container->registerForAutoconfiguration(FtpClientBehaviorFactoryInterface::class)
            ->addTag('vdm_library.ftp_decorator_factory')
        ;

        $loader = new YamlFileLoader(
            $container,
            new FileLocator(__DIR__.'/../Resources/config')
        );
        $loader->load('services.yaml');
    }

    /**
     * {@inheritDoc}
     */
    public function getAlias()
    {
        return 'vdm_library_ftp_transport';
    }
}
