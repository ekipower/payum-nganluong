<?php
/**
 * This file is part of the EkiPayumNganluongBundle package.
 *
 * (c) EkiPower <http://ekipower.github.com/>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */ 

namespace Eki\Payum\Nganluong\Action\ATMOnline;

use Eki\Payum\Nganluong\Action\CaptureAction as BaseCaptureAction;
use Eki\Payum\Nganluong\Request\Log;
use Eki\Payum\Nganluong\Request\ATMOnline\DetermineBank;

use Payum\Core\Exception\RequestNotSupportedException;

class CaptureAction extends BaseCaptureAction
{
	/**
	* Process payment info (method, type, bakn, ...)
	* ) 
	* @param mixed $request
	* @param mixed $model
	* 
	* $return void
	*/
	protected function processPaymentInfo($request, $model)
	{
		$this->payment->execute(new Log('Payment method is ATM_ONLINE. It must obtain bank code first.', $this));
		if ( false == $model->validateNotEmpty(array('bank_code'), false) )
		{
			$this->payment->execute(new Log('Get bank code...', $this));
			try
			{
	            $this->payment->execute($bankRequest = new DetermineBank);
				$bank = $bankRequest->determine();
				
				$model['bank_code'] = $bank->getCode();
            } 
			catch (RequestNotSupportedException $e) 
			{
                throw new \LogicException('Bank code has to be set explicitly or there has to be an action that supports DetermineBank request.');
            }
		}
	}
}
