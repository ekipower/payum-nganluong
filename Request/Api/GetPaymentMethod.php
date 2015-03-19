<?php
/**
 * This file is part of the EkiPayumNganluong package.
 *
 * (c) EkiPower <http://ekipower.github.com/>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */ 

namespace Eki\Payum\Nganluong\Request\Api;

use Payum\Core\Request\Generic;

class GetPaymentMethod extends Generic
{
	/**
	* 
	* @var array
	* 
	*/
	private $info;
	
	/**
	* Get payment info (method. type) 
	* 
	*/
	public function getInfo()
	{
		return $this->info;
	}

	public function setInfo(array $info)
	{
		$this->info = $info;
	}	
}