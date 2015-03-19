<?php
/**
 * This file is part of the EkiPayumNganluong package.
 *
 * (c) EkiPower <http://ekipower.github.com/>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */ 

namespace Eki\Payum\Nganluong\Action;

use Eki\Payum\Nganluong\Api;
use Eki\Payum\Nganluong\Api\State\StateInterface;

use Payum\Core\Action\ActionInterface;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\Request\GetStatusInterface;
use Payum\Core\Exception\RequestNotSupportedException;

class StatusAction implements ActionInterface, StateInterface
{
    /**
     * {@inheritDoc}
     *
     * @param GetStatusInterface $request
     */

    /**
     * {@inheritDoc}
     *
     * @param GetStatusInterface $request
     */
    public function execute($request)
    {
        RequestNotSupportedException::assertSupports($this, $request);

        $model = new ArrayObject($request->getModel());

        $state = $model['state'];

        if (null === $state ||
            StateInterface::STATE_WAITING === $state
        ) {
            $request->markNew();

            return;
        }

        if (StateInterface::STATE_REPLIED === $state ||
            StateInterface::STATE_NOTIFIED === $state
        ) {
            $request->markPending();

            return;
        }

        if (StateInterface::STATE_CONFIRMED === $state) {
            $request->markCaptured();

            return;
        }

        $request->markFailed();
    }

    /**
     * {@inheritDoc}
     */
    public function supports($request)
    {
        return
            $request instanceof GetStatusInterface &&
            $request->getModel() instanceof \ArrayAccess
        ;
    }
}
