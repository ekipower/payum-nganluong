<?php
/**
 * This file is part of the EkiPayumBundle package.
 *
 * (c) EkiPower <http://ekipower.github.com/>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */ 

namespace Eki\Payum\Nganluong\Action;

use Payum\Be2Bill\Api;
use Payum\Core\Action\PaymentAwareAction;
use Payum\Core\ApiAwareInterface;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\Exception\UnsupportedApiException;
use Payum\Core\Request\Capture;
use Payum\Core\Request\GetHttpRequest;
use Payum\Core\Reply\HttpPostRedirect;

class CaptureOnsiteAction extends PaymentAwareAction implements ApiAwareInterface
{
    /**
     * @var Api
     */
    protected $api;

    /**
     * {@inheritDoc}
     */
    public function setApi($api)
    {
        if (false === $api instanceof Api) {
            throw new UnsupportedApiException('Not supported.');
        }

        $this->api = $api;
    }

    /**
     * {@inheritDoc}
     *
     * @param Capture $request
     */
    public function execute($request)
    {
        /** @var $request Capture */
        RequestNotSupportedException::assertSupports($this, $request);

        $model = ArrayObject::ensureArrayObject($request->getModel());

        if ( null !== $model['token'] )
			return;

        if (false == $model['return_url'] && $request->getToken()) {
            $model['return_url'] = $request->getToken()->getTargetUrl();
        }

        if (false == $model['cancel_url'] && $request->getToken()) {
            $model['cancel_url'] = $request->getToken()->getTargetUrl();
        }

        $this->payment->execute(new SetExpressCheckout($model));

        if ( $model['error_code'] == '00' )
		{
			if ( !isset($model['checkout_url']) )
			{
				throw new \LogicException('Payment gateway Nganluong is not returned "checkout_url"');
			}
			
            throw new HttpRedirect(	$model['checkout_url'] );
		}
        else
		{
            return;   // failed
        }
    }

    /**
     * {@inheritDoc}
     */
    public function supports($request)
    {
        return
            $request instanceof Capture &&
            $request->getModel() instanceof \ArrayAccess
        ;
    }
}
