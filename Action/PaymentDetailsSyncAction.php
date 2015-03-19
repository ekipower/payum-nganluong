<?php
/**
 * This file is part of the EkiPayumNganluongBundle package.
 *
 * (c) EkiPower <http://ekipower.github.com/>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */ 

namespace Eki\Payum\Nganluong\Action;

use Eki\Payum\Nganluong\Api\TransactionStatus;
use Eki\Payum\Nganluong\Api\State\StateInterface;

use Eki\Payum\Nganluong\Api;
use Eki\Payum\Nganluong\Request\Api\GetTransactionDetails;

use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\Request\Sync;
use Payum\Core\Action\PaymentAwareAction;
use Payum\Core\Exception\RequestNotSupportedException;

class PaymentDetailsSyncAction extends PaymentAwareAction
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

        $this->payment->execute(new GetTransactionDetails($model));
		
        if ( isset($model['error_code']) && $model['error_code'] === Errors::ERRCODE_NO_ERROR )
		{
			if ( isset($model['transaction_status']) )
			{
				if ( $model['transaction_status'] === TransactionStatus::PAID )
				{
					$model['state'] = StateInterface::STATE_CONFIRMED;
				}
				else if ( $model['transaction_status'] === TransactionStatus::NOT_PAID )
				{
					$model['state'] = StateInterface::STATE_ERROR;
				}
				else if ( $model['transaction_status'] === TransactionStatus::PAID_WAITING_FOR_PROCESS )
				{
					$model['state'] = StateInterface::STATE_NOTIFIED;
				}
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