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

final class TransactionStatus
{
	const PAID = '00';
	const PAID_WAITING_FOR_PROCESS = '01';
	const NOT_PAID = '02';
}
