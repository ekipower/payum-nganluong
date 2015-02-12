<?php
/**
 * This file is part of the EkiPayumNganluong package.
 *
 * (c) EkiPower <http://ekipower.github.com/>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */ 

namespace Eki\Payum\Nganluong\Bridge\Symfony;

use Eki\Payum\Nganluong\Bridge\Symfony\BasePaymentFactory;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

class NganluongOnsitePaymentFactory extends BasePaymentFactory
{
    /**
     * {@inheritdoc}
     */
    public function create(ContainerBuilder $container, $contextName, array $config)
    {
        if (false == class_exists('\Eki\Payum\Nganluong\OnsitePaymentFactory')) {
            throw new RuntimeException('Cannot find redsys payment factory class');
        }

        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/Resources/config'));
        $loader->load('nganluong.xml');

        return parent::create($container, $contextName, $config);
    }

    public function getName()
    {
        return 'nganluong_onsite';
    }
}
