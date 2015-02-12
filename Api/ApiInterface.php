<?php
/**
 * This file is part of the EkiPayumNganluong package.
 *
 * (c) EkiPower <http://ekipower.github.com/>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */ 

namespace Eki\Payum\Nganluong\Api;

interface ApiInterface
{
    public function setExpressCheckout(array $fields);
	
	public function getExpressCheckoutDetails(array $fields);

    public function prepareOnsiteDetails(array $paymentDetails);

    public function getMissingDetails(array $details);
}
