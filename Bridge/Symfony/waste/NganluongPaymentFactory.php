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

namespace Eki\Payum\Nganluong\Bridge\Symfony\BasePaymentFactory;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class NganluongPaymentFactory extends BasePaymentFactory
{
    protected function addActions(Definition $paymentDefinition, ContainerBuilder $container, $contextName, array $config)
    {
    	$this->paymentAddAction($container, $paymentDefinition, 'Capture', null, $contextName);
    	$this->paymentAddAction($container, $paymentDefinition, 'Notify', null,$contextName);
    	$this->paymentAddAction($container, $paymentDefinition, 'Status', null, $contextName);
    	$this->paymentAddAction($container, $paymentDefinition, 'FillOrderDetails', $actionPrefixClass , $contextName,
			array('@payum.security.token_factory')
		);
	}

    public function getName()
    {
        return 'nganluong';
    }
}