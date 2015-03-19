<?php
/**
 * This file is part of the EkiPayumNganluong package.
 *
 * (c) EkiPower <http://ekipower.github.com/>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */ 

namespace Eki\Payum\Nganluong\Request;

class Log
{
	const DEFAULT_PREFIX = 'Nganluong';
	
	/**
	* 
	* @var string
	* 
	*/
	private $msg;
	
	/**
	* 
	* @var string
	* 
	*/
	private $type;
	
	/**
	* 
	* @var string
	* 
	*/
	private $prefix;
	
	/**
	* 
	* @var object
	* 
	*/
	private $context;
	
	/**
	* 
	* @param string $msg
	* @param string $type
	* @param object $context Contex on which log shows
	* @param string $prefix
	* 
	*/
	public function __construct( $msg, $context = null, $prefix = self::DEFAULT_PREFIX, $type = 'debug' )
	{
		$this->msg = $msg;
		$this->prefix = $prefix;
		$this->context = $context;
		$this->type = $type;
	}
	
	/**
	* Get type of message (info, debug, ....) 
	* 
	*/
	public function getType()
	{
		return $this->type;
	}
	
	/**
	* Get log message
	* 
	*/
	public function getMessage()
	{
		return '['.$this->prefix.( $this->context === null ? '' : (' - '.get_class($this->context)) ).'] ' . $this->msg;
	}
}