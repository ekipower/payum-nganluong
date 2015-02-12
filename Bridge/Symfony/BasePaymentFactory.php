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

use Payum\Bundle\PayumBundle\DependencyInjection\Factory\Payment\AbstractPaymentFactory;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\DefinitionDecorator;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

abstract class BasePaymentFactory extends AbstractPaymentFactory
{
	protected $actionPrefixClass = 'Eki\\Payum\\Nganluong\\Action\\';

    protected function paymentAddAction(
		ContainerBuilder $container,
		Definition $paymentDefinition,
		$actionName,
		$actionPrefixClass,
		$contextName,
		array $arguments = array(),
		$public = false
	)
	{
		$actionPrefixClass = $actionPrefixClass == null ? $this->$actionPrefixClass : $actionPrefixClass;
		
        $actionDefinition = new Definition($actionPrefixClass.$actionName.'Action');
		$actionDefinition->setPublic((bool)$public);
		foreach($arguments as $argument)
		{
	        $actionDefinition->addArgument($argument);
		}
        $actionId = "payum." . $this->getName() . ".action." . $this->from_camel_case($actionName);
        $container->setDefinition($actionId, $actionDefinition);

        $paymentDefinition->addMethodCall('addAction', array(new Reference($actionId)));
	}

	private function from_camel_case($input) 
	{
	  preg_match_all('!([A-Z][A-Z0-9]*(?=$|[A-Z][a-z0-9])|[A-Za-z][a-z0-9]+)!', $input, $matches);
	  $ret = $matches[0];
	  foreach ($ret as &$match) {
	    $match = $match == strtoupper($match) ? strtolower($match) : lcfirst($match);
	  }
	  return implode('_', $ret);
	}

    /**
     * {@inheritDoc}
     */
    public function addConfiguration(ArrayNodeDefinition $builder)
    {
        parent::addConfiguration($builder);
        
        $builder->children()
            ->scalarNode('merchant_id')->isRequired()->cannotBeEmpty()->end()
            ->scalarNode('merchant_password')->isRequired()->cannotBeEmpty()->end()
            ->scalarNode('receiver_email')->isRequired()->cannotBeEmpty()->end()
            ->booleanNode('sandbox')->defaultTrue()->end()
            ->scalarNode('sandbox_url')->end()
        ->end();
    }

    /**
     * {@inheritDoc}
     */
    protected function addApis(Definition $paymentDefinition, ContainerBuilder $container, $contextName, array $config)
    {
		$options = array(
            'merchant_id' => $config['merchant_id'],
            'merchant_password' => $config['merchant_password'],
            'receiver_email' => $config['receiver_email'],
            'sandbox' => $config['sandbox']
		);
		if ( isset($config['sandbox_url']) )
		{
			$options['sandbox_url'] = $config['sandbox_url'];
		}
		
        $apiDefinition = new DefinitionDecorator('eki.payum.nganluong.api.prototype');
        $apiDefinition->replaceArgument(0, $options);
        $apiDefinition->setPublic(true);
        $apiId = 'payum.context.'.$contextName.'.api';
        $container->setDefinition($apiId, $apiDefinition);
        $paymentDefinition->addMethodCall('addApi', array(new Reference($apiId)));
    }
}