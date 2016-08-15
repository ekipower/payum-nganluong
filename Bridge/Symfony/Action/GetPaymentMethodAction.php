<?php
/**
 * This file is part of the EkiPayumBundle package.
 *
 * (c) EkiPower <http://ekipower.github.com/>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */ 

namespace Eki\Payum\Nganluong\Bridge\Symfony\Action;

use Eki\Payum\Nganluong\Request\GetPaymentMethod;
use Eki\Payum\Nganluong\Api\PaymentTypes;

use Payum\Core\Action\PaymentAwareAction;

abstract class GetPaymentMethodAction extends PaymentAwareAction
{
	const DEFAULT_PAYMENT_TYPE = PaymentTypes::TYPE_IMMEDIATE;

    /**
     * {@inheritDoc}
     *
     * @param $request GetPaymentMethod
     */
    public function execute($request)
    {
        if (!$this->supports($request)) {
            throw RequestNotSupportedException::createActionNotSupported($this, $request);
        }

        $model = ArrayObject::ensureArrayObject($request->getModel());

		$request->setInfo($this->getMethodCode($model));
    }

    /**
     * {@inheritDoc}
     */
    public function supports($request)
    {
        return 
			$request instanceof GetPaymentMethod &&
			$request->getModel() instanceof \ArrayAccess
		;
    }

	/**
	* Gets payment method code from method name
	* 
	* @param object $model
	* 
	* @return array 
	*/	
	protected function getMethodCode($model)
	{
		$keywords = array(
			'visa' => 'VISA',
			'atm' => 'ATM_ONLINE',
			'nl' => 'NL',
			'ví' => 'NL',
			'ttvp' => 'TTVP',
		);

		$paymentMethodName = $this->getPaymentMethodName($model);
		$info = array();
		foreach($keywords as $key => $paymentMethod)
		{
			if ( strpos(strtolower($paymentMethodName), $key) !== false  )
			{
				$info['payment_method'] = $paymentMethod;
				break;
			}	
		}
		
		if ( !isset($info['payment_method']) )
		{
			throw new \LogicException('No payment method supports.');
		}
		
		$info['payment_type'] = 1;
		
		return $info;
	}

	/**
	* Get human payment method name
	* 
	* @param object $model
	* 
	* @return string 
	*/	
	abstract protected function getPaymentMethodName($model);
}
