<?php
/**
 * This file is part of the EkiSyliusPayumBundle package.
 *
 * (c) EkiPower <http://ekipower.github.com/>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */ 

namespace Eki\Payum\Nganluong\Bridge\Sylius\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\Config\FileLocator;

class ConfigLoader
{
    public function load(array $configs, ContainerBuilder $container)
    {
		$loader = new XmlFileLoader($container, new FileLocator( __DIR__ . '/../Resources/config' ));
        $loader->load( 'nganluong.xml' );
    }
}
