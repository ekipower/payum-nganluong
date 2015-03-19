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

class ApiException extends \RuntimeException
{
	public function __construct($code)
	{
		parent::__construct(Errors::ErrorMessages($code), $code);
	}
}
