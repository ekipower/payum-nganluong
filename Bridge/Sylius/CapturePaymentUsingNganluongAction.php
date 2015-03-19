<?php
/**
 * This file is part of the EkiSyliusPayumBundle package.
 *
 * (c) EkiPower <http://ekipower.github.com/>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */ 

namespace Eki\Payum\Nganluong\Bridge\Sylius;

use Payum\Core\Security\GenericTokenFactoryInterface;
use Payum\Core\Security\TokenInterface;
use Sylius\Bundle\PayumBundle\Payum\Action\AbstractCapturePaymentAction;
use Sylius\Component\Core\Model\AdjustmentInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Payment\Model\PaymentInterface;

class CapturePaymentUsingNganluongAction extends AbstractCapturePaymentAction
{
	const DEFAULT_PAYMENT_TYPE = 1;
	
    /**
     * @var GenericTokenFactoryInterface
     */
    protected $tokenFactory;

    /**
     * @param GenericTokenFactoryInterface $tokenFactory
     */
    public function __construct(GenericTokenFactoryInterface $tokenFactory)
    {
        $this->tokenFactory = $tokenFactory;
    }

    /**
     * {@inheritdoc}
     */
    protected function composeDetails(PaymentInterface $payment, TokenInterface $token)
    {
        if ($payment->getDetails()) {
            return;
        }

        $order = $payment->getOrder();

        $details = array();

		$details['payment_method'] = $this->apiMethod(strtolower($payment->getMethod()->getName()));
		$details['payment_type'] = self::DEFAULT_PAYMENT_TYPE;

        $details['order_code'] = $order->getNumber().'-'.$payment->getId();
        $details['cur_code'] = $order->getCurrency();
        $details['total_amount'] = round($order->getTotal() / 100, 2);
		$details['total_item'] = count($order->getItems());

        $m = 0;
        foreach ($order->getItems() as $item) 
		{
            $details['item_name'.$m] = $item->getId();
            $details['item_amount'.$m] = round($item->getTotal()/$item->getQuantity()/100, 2);
            $details['item_quantity'.$m] = $item->getQuantity();

            $m++;
        }

        if (0 !== $taxTotal = $this->calculateNonNeutralTaxTotal($order)) {
            $details['tax_amount']  = $taxTotal;
        }

        if (0 !== $promotionTotal = $order->getAdjustmentsTotal(AdjustmentInterface::PROMOTION_ADJUSTMENT)) {
            $details['discount_amount']  = $promotionTotal;
        }

        if (0 !== $shippingTotal = $order->getAdjustmentsTotal(AdjustmentInterface::SHIPPING_ADJUSTMENT)) {
            $details['fee_shipping']  = $shippingTotal;
        }

        $payment->setDetails($details);
    }

	private function apiMethod($methodName)
	{
		$mappings = array(
			'nganluong_visa' => 'VISA',
			'nganluong_atm' => 'ATM_ONLINE',
			'van phong ngan luong' => 'TTVP',
			'vi ngan luong' => 'NL',
		);
		
		if ( in_array( $methodName, array_keys($mappings) ) )	
		{
			return $mappings[$methodName];
		}
		else
		{
			return $methodName;	
		}
	}

    private function calculateNonNeutralTaxTotal(OrderInterface $order)
    {
        $nonNeutralTaxTotal = 0;
        foreach ($order->getAdjustments(AdjustmentInterface::TAX_ADJUSTMENT) as $taxAdjustment) {
            if (!$taxAdjustment->isNeutral()) {
                $nonNeutralTaxTotal = $taxAdjustment->getAmount();
            }
        }

        return $nonNeutralTaxTotal;
    }
}
