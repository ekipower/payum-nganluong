<?php
/**
 * This file is part of the EkiPayumBundle package.
 *
 * (c) EkiPower <http://ekipower.github.com/>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */ 

namespace Eki\Payum\Nganluong\Action\Api;

use Eki\Payum\Nganluong\Action\Api\BaseApiAwareAction;
use Eki\Payum\Nganluong\Request\Api\SetExpressCheckout;
use Eki\Payum\Nganluong\Request\Api\GetPaymentMethod;

use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\Exception\LogicException;

class SetExpressCheckoutAction extends BaseApiAwareAction
{
    /**
     * {@inheritDoc}
     */
    public function execute($request)
    {
        /** @var $request SetExpressCheckout */
        RequestNotSupportedException::assertSupports($this, $request);

        $model = ArrayObject::ensureArrayObject($request->getModel());

/*
		if ( false == $model->validateNotEmpty( array('payment_method', 'payment_type'), false ) )
		{
			try
			{
				$this->execute( $paymentInfoRequest = new GetPaymentMethod($model) );
				$paymentInfo = $paymentInfoRequest->getInfo();
				$model['payment_method'] = $paymentInfo['payment_method'];
				$model['payment_type'] = $paymentInfo['payment_type'];
            } 
			catch (RequestNotSupportedException $e) {
                throw new LogicException('Payment info (method, type, ...)) has to be set explicitly or there has to be an action that supports PaymentMethod request.');
            }
		}
*/

		$model['payment_method'] = 'VISA';
		$model['payment_type'] = 1;
				
		$model->validateNotEmpty(array('total_amount', 'cur_code', 'order_code', 'payment_method', 'payment_type'));

        $model->replace(
            $this->api->setExpressCheckout((array) $model)
        );
    }

    /**
     * {@inheritDoc}
     */
    public function supports($request)
    {
        return
            $request instanceof SetExpressCheckout &&
            $request->getModel() instanceof \ArrayAccess
        ;
    }
}
