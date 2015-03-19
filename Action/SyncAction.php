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

//use Eki\Payum\Nganluong\Api;
use Eki\Payum\Nganluong\Api\State\StateInterface;
use Eki\Payum\Nganluong\Api\Errors;
use Eki\Payum\Nganluong\Api\TransactionStatus;
use Eki\Payum\Nganluong\Request\Api\GetTransactionDetails;

use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\Request\Sync;
use Payum\Core\Action\PaymentAwareAction;
use Payum\Core\Exception\RequestNotSupportedException;

class SyncAction extends PaymentAwareAction
{
    /**
     * {@inheritDoc}
     */
    public function execute($request)
    {
        /** @var $request Sync */
        RequestNotSupportedException::assertSupports($this, $request);
        
        $model = ArrayObject::ensureArrayObject($request->getModel());

        if (false == $model['token']) {
            return;
        }

        $copiedModel = new ArrayObject(array(
            'token' => $model['token'],
        ));
        
        $this->payment->execute(new GetTransactionDetails($copiedModel));
		if ( $copiedModel['error_code'] === Errors::ERRCODE_NO_ERROR )
		{
			if ( $copiedModel['transaction_status'] )
			{
				if ( $model['status'] != null )
				{
					if ( $copiedModel['transaction_status'] === TransactionStatus::PAID_WAITING_FOR_PROCESS )
					{
						$model['status'] = StateInterface::STATE_NOTIFIED;
					}
					else if ( $copiedModel['transaction_status'] === TransactionStatus::PAID )
					{
						$model['status'] = StateInterface::STATE_CONFIRMED;
					}
				}
				else
				{
					throw new \RuntimeException('Unknown error....');	
				}
			}
			else
			{
				throw new \RuntimeException('Unknown error.');	
			}
		}
    }

    /**
     * {@inheritDoc}
     */
    public function supports($request)
    {
        if (false == $request instanceof Sync) {
            return false;
        }

        $model = $request->getModel();
        if (false == $model instanceof \ArrayAccess) {
            return false;
        }
    }
}