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

use Eki\Payum\Nganluong\Request\Api\SetExpressCheckout;

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

        if (null === $model['total_amount']) {
            throw new LogicException('The "total_amount" must be set.');
        }
        if (null === $model['cur_code']) {
            throw new LogicException('The "cur_code" must be set.');
        }
        if (null === $model['order_code']) {
            throw new LogicException('The "order_code" must be set.');
        }
        if (null === $model['payment_method']) {
            throw new LogicException('The "payment_method" must be set.');
        }

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
