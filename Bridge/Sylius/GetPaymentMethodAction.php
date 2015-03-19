<?php
/**
 * This file is part of the EkiSyliusPayumBundle package.
 *
 * (c) EkiPower <http://ekipower.github.com/>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */ 

namespace Eki\Payum\Nganluong\Bridge\Sylius;

use Payum\Core\Action\PaymentAwareAction;
use Payum\Core\Exception\RequestNotSupportedException;
use Sylius\Component\Payment\Model\PaymentInterface;

use Eki\Payum\Nganluong\Request\Api\GetPaymentMethod;
use Eki\Payum\Nganluong\Api\PaymentTypes;

class GetPaymentMethodAction extends PaymentAwareAction
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

        /** @var $payment PaymentInterface */
        $payment = $request->getModel();

		$info = array();
		$info['payment_type'] = self::DEFAULT_PAYMENT_TYPE;
		$info['payment_method'] = $this->getMethodCode(strtolower($payment->getMethod()->getName()));
		
		$request->setInfo($info);
    }

    /**
     * {@inheritDoc}
     */
    public function supports($request)
    {
        return
            $request instanceof GetPaymentMethod &&
            $request->getModel() instanceof PaymentInterface
        ;
    }

	/**
	* Gets payment method code 
	* 
	* @param string $name
	*
	* @return string|null 
	*/	
	protected function getMethodCode($name)
	{
		$mappings = array(
			'nganluong_visa' => 'VISA',
			'nganluong_atm' => 'ATM_ONLINE',
			'nganluong_ttvp' => 'TTVP',
			'nganluong_nl' => 'NL',
		);
		
		if ( in_array( $name, array_keys($mappings) ) )	
		{
			return $mappings[$name];
		}
	}
}
