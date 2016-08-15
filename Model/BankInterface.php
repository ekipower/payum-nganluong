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

interface BankInterface
{
	/**
	* Get code of bank
	*
	* @return string 
	*/
	public function getCode();
	
	/**
	* Sets bank code
	* 
	* @param string $code
	*
	* @return this 
	*/
	public function setCode($code);
	
	/**
	* Get the name of bank
	*
	* @return string
	*/
	public function getName();
	
	/**
	* Sets the name of bank
	* 
	* @param string $name
	* 
	* @return this
	*/
	public function setName($name);
	
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
