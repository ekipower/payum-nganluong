<?php
/**
 * This file is part of the EkiPayum package.
 *
 * (c) EkiPower <http://ekipower.github.com/>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */ 

namespace Eki\Payum\Nganluong;

use Eki\Payum\Nganluong\Action\CaptureAction;
use Eki\Payum\Nganluong\Action\FillOrderDetailsAction;
use Eki\Payum\Nganluong\Action\StatusAction;

use Eki\Payum\Nganluong\Action\Api\SetExpressCheckoutAction;
use Eki\Payum\Nganluong\Action\Api\GetTransactionDetailsAction;

use Payum\Core\Action\ExecuteSameRequestWithModelDetailsAction;
use Payum\Core\Action\GetHttpRequestAction;
use Payum\Core\Payment;
use Payum\Core\Extension\EndlessCycleDetectorExtension;

abstract class PaymentFactory
{
    /**
     * @param Api $api
     *
     * @return \Payum\Core\Payment
     */
    public static function create(Api $api)
    {
        $payment = new Payment;

        $payment->addApi($api);

        $payment->addExtension(new EndlessCycleDetectorExtension);

        $payment->addAction(new SetExpressCheckoutAction);
        $payment->addAction(new GetTransactionDetailsAction);

        $payment->addAction(new CaptureAction);
        $payment->addAction(new FillOrderDetailsAction);
        $payment->addAction(new StatusAction);
        $payment->addAction(new ExecuteSameRequestWithModelDetailsAction);
        $payment->addAction(new GetHttpRequestAction);

        return $payment;
    }

    /**
     */
    private  function __construct()
    {
    }
}
