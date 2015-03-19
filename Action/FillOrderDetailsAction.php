<?php
/**
 * This file is part of the EkiPayumBundle package.
 *
 * (c) EkiPower <http://ekipower.github.com/>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */ 

namespace Eki\Payum\Nganluong\Action;

use Payum\Core\Action\ActionInterface;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\Request\FillOrderDetails;
use Payum\Core\Security\GenericTokenFactoryInterface;

class FillOrderDetailsAction implements ActionInterface
{
    /**
     * @var GenericTokenFactoryInterface
     */
    protected $tokenFactory;

    /**
     * @param GenericTokenFactoryInterface $tokenFactory
     */
    public function __construct(GenericTokenFactoryInterface $tokenFactory = null)
    {
        $this->tokenFactory = $tokenFactory;
    }

    /**
     * {@inheritDoc}
     *
     * @param FillOrderDetails $request
     */
    public function execute($request)
    {
        RequestNotSupportedException::assertSupports($this, $request);

        $order = $request->getOrder();
        $divisor = pow(10, $order->getCurrencyDigitsAfterDecimalPoint());

        $details = $order->getDetails();
		
        $details['order_code'] = $order->getNumber();
        $details['total_amount'] = $order->getTotalAmount() / $divisor;
        $details['cur_code'] = $order->getCurrencyCode();
		$details['order_description'] = $order->getDescription();
		
		$details['buyer_email'] = $order->getClientEmail();

		// need more, fill later....

/*
R	order_code
R	total_amount
R   cur_code

payment_method
payment_type

R order_description
tax_amount
discount_amount
fee_shipping
R return_url
R cancel_url
time_limit
buyer_fullname
R buyer_email
buyer_mobile
buyer_address
affiliate_code
total_item
item_name1
item_quantity1
item_amount1
item_url1
*/
        $order->setDetails($details);
    }

    /**
     * {@inheritDoc}
     */
    public function supports($request)
    {
        return $request instanceof FillOrderDetails;
    }
}
