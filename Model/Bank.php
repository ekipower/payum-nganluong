<?php
/**
 * This file is part of the EkiPayumNganluong package.
 *
 * (c) EkiPower <http://ekipower.github.com/>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */ 

namespace Eki\Payum\Nganluong\Model;

use Eki\Payum\Nganluong\Api\BankCodes;
use Eki\Payum\Nganluong\Api\ApiException;

class Bank implements BankInterface
{
	protected $code;
	protected $name;
	
	//protected $onlineSupported;
	//protected $machineSupported;
	
	/**
	* Get code of bank
	*
	* @return string 
	*/
	public function getCode()
	{
		return $this->code;
	}
	
	/**
	* Sets bank code
	* 
	* @param string $code
	*
	* @return this 
	*/
	public function setCode($code)
	{
		$this->code = $code;
		
		return $this;
	}
	
	/**
	* Get the name of bank
	*
	* @return string
	*/
	public function getName()
	{
		return $ths->name;
	}
	
	/**
	* Sets the name of bank
	* 
	* @param string $name
	* 
	* @return this
	*/
	public function setName($name)
	{
		$this->name = $name;
		
		return $this;
	}
	
	/**
	* Checks if online support
	*
	* @return bool 
	* 
	* @throw
	*/
	//public function isOnlineSupported();
	
	/**
	* Checks if ATM machine support
	*
	* @return bool
	* 
	* @throw
	*/
	//public function isATMMachineSupport();
}
