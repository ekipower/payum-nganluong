<?php
/**
 * This file is part of the EkiPayum package.
 *
 * (c) EkiPower <http://ekipower.github.com/>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */ 

namespace Eki\Payum\Nganluong\Api;

use Eki\Payum\Nganluong\Api\Errors;
use Eki\Payum\Nganluong\Api\PaymentMethods;
use Eki\Payum\Nganluong\Api\ApiException;

use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;

abstract class AbstractApi implements ApiInterface
{
	/**
	* @inheritdoc 
	*/	
	public function doFunction(array $fields)
	{
		if ( isset($fields['function']) && in_array($fields['function'], array_keys($functions = $this->getSupportedFunctions())) )
		{
			try
			{
				$method = 'doFunction_'.$functions[$fields['function']];
				$result = $this->$method($fields);
			}
			catch(ApiException $e)
			{
				$result = $this->getErrorMessage($e->getCode());				
			}
		}
		else
		{
			$result = $this->getErrorResult(Errors::ERRCODE_API_FUNCTION_WRONG);
		}
		
		return $result;
	}

	public function getErrorResult($errorCode)
	{
		$result = array();
		$result['error_code'] = $errorCode;
		$result['description'] = Errors::ErrorMessages()[$errorCode];
		
		return $result;
	}

	/**
	* Get supported function
	*/
	abstract protected function getSupportedFunctions();
	
	/**
	* @inheritdoc 
	*/
	public function fillErrorMessages($errorCode, array $others = array())
	{
		$errorMessages = array(
			'error_code' => $errorCode,
			'error_description' => $this->getErrorMessage($errorCode)
		) + $others;
		
		return $errorMessages;
	}
}
