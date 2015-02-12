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

use Eki\Payum\Nganluong\Request\Api\GetTransactionDetails;

use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\Exception\LogicException;
use Payum\Core\Exception\RequestNotSupportedException;

class GetTransactionDetailsAction extends BaseApiAwareAction
{
    /**
     * {@inheritDoc}
     */
    public function execute($request)
    {
        /** @var $request GetTransactionDetails */
        RequestNotSupportedException::assertSupports($this, $request);
        
        $model = ArrayObject::ensureArrayObject($request->getModel());

        if (false == $model['token']) {
            throw new LogicException('"token" must be set. Have you run SetExpressCheckoutAction?');
        }

		$model->replace( 
			$this->api->getTransactionDetails( array( 'token' => $model['token'] ) ) 
		);
    }

    /**
     * {@inheritDoc}
     */
    public function supports($request)
    {
        return 
            $request instanceof GetTransactionDetails &&
            $request->getModel() instanceof \ArrayAccess
        ;
    }
}