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

interface ApiInterface
{
	/**
	* Get api version 
	* 
	* @return string
	*/
	public function getVersion();
	
	/**
	* Check input fields
	* 
	* @param array $fields
	* 
	* @return
	*/
	public function checkFields(array $fields);
	
	/**
	* Do an api function
	* 
	* @param array $fields
	*
	* @return string $xmlContent 
	*/	
	public function doFunction(array $fields);
	
	/**
	* Fill error messages 
	* 
	* @param undefined $errorCode
	* @param undefined $others
	*
	* @return array 
	*/
	public function fillErrorMessages($errorCode, array $others = array());
}
