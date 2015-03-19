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

use Eki\Payum\Nganluong\Api;
use Eki\Payum\Nganluong\Api\Errors;
use Eki\Payum\Nganluong\Api\TransactionStatus;
use Eki\Payum\Nganluong\Api\State\StateInterface;
use Eki\Payum\Nganluong\Request\Log;
use Eki\Payum\Nganluong\Request\Api\SetExpressCheckout;
use Eki\Payum\Nganluong\Request\Api\GetTransactionDetails;

use Payum\Core\Action\PaymentAwareAction;
use Payum\Core\ApiAwareInterface;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\Exception\LogicException;
use Payum\Core\Reply\HttpRedirect;
use Payum\Core\Request\Capture;
use Payum\Core\Request\Sync;
use Payum\Core\Request\GetHttpRequest;
use Payum\Core\Request\ObtainCreditCard;
use Payum\Core\Exception\UnsupportedApiException;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\Security\SensitiveValue;
use Payum\Core\Security\GenericTokenFactoryInterface;

class CaptureAction extends PaymentAwareAction implements ApiAwareInterface
{
    /**
     * @var Api
     */
    protected $api;

	/**
	* 
	* @var Payum\Core\Security\GenericTokenFactoryInterface
	* 
	*/
	protected $tokenFactory;
    
    /**
     * {@inheritDoc}
     */
    public function setApi($api)
    {
        if (false == $api instanceof Api) {
            throw new UnsupportedApiException('Api not supported.');
        }
        
        $this->api = $api;
    }

	/**
	* Sets token factory
	* 
	* @param GenericTokenFactoryInterface $tokenFactory
	* 
	*/
	public function setTokenFactory(GenericTokenFactoryInterface $tokenFactory)
	{
		$this->tokenFactory = $tokenFactory;	
	}

    /**
     * {@inheritDoc}
     */
    public function execute($request)
    {
        /** @var $request Capture */
        RequestNotSupportedException::assertSupports($this, $request);

        $model = ArrayObject::ensureArrayObject($request->getModel());

        if ( !isset($model['token']) || false === $model['token'] ) 
		{
/*			
            if (false == $model['return_url'] && $request->getToken()) {
                $model['return_url'] = $request->getToken()->getTargetUrl();
            }

            if (false == $model['cancel_url'] && $request->getToken()) {
                $model['cancel_url'] = $request->getToken()->getTargetUrl();
            }
*/
            if (false == $model['return_url'] || false == $model['cancel_url']) 
			{
	            $notifyToken = $this->tokenFactory->createNotifyToken(
	                $request->getToken()->getPaymentName(),
	                $request->getToken()->getDetails()
	            );
				
                $model['return_url'] = false == $model['return_url'] ? $notifyToken->getTargetUrl() : $model['return_url'];
                $model['cancel_url'] = false == $model['cancel_url'] ? $notifyToken->getTargetUrl() : $model['cancel_url'];
            }

			$model['state'] = StateInterface::STATE_WAITING;
            $this->payment->execute(new SetExpressCheckout($model));

            if ( isset($model['error_code']) && $model['error_code'] === Errors::ERRCODE_NO_ERROR )
			{
		        if (isset($model['checkout_url'])) 
				{
					$model['state'] = StateInterface::STATE_REPLIED;
					$this->payment->execute(new Log('checkout_url='.$model['checkout_url']));
		            throw new HttpRedirect($model['checkout_url']);
		        }
				else
				{
					$model['state'] = StateInterface::STATE_ERROR;
					$this->execute(new Log('No checkout_url returned.'));
				}
            }
/*			
			else
			{
				$model['state'] = StateInterface::STATE_ERROR;	
				
				$errMsg = 'Error from SetExpressCheckout.';
				$errMsg .= '{error_code='.$model['error_code'].'}';
				$errMsg .= '{description='.$model['description'].'}';
				$this->payment->execute(new Log($errMsg));
			}
        }
		else
		{
	        $this->payment->execute(new GetTransactionDetails($model));
	        if ( isset($model['error_code']) && $model['error_code'] === Errors::ERRCODE_NO_ERROR )
			{
				if ( !isset($model['transaction_status']) )
				{
					$model['state'] = StateInterface::STATE_ERROR;
					$this->execute(new Log('GetTransactionDetails has no transaction status.'));
				}
				
				if ( $model['transaction_status'] === TransactionStatus::PAID)
				{
					$model['state'] = StateInterface::STATE_CONFIRMED;
					$this->execute(new Log('GetTransactionDetails -> Paid.'));
					
					return;
				}

				if ( $model['transaction_status'] === TransactionStatus::PAID_WAITING_FOR_PROCESS)
				{
					$model['state'] = StateInterface::STATE_NOTIFIED;
					$this->execute(new Log('GetTransactionDetails -> Waiting for paying.'));
					
					return;
				}
				
				if ( $model['transaction_status'] === TransactionStatus::NOT_PAID)
				{
					$model['state'] = StateInterface::STATE_ERROR;
					$this->execute(new Log('GetTransactionDetails-> Not paid'));
					
					return;
				}
			}
			else
			{
				$model['state'] = StateInterface::STATE_ERROR;
				$this->execute(new Log('Unknown error when checking payment.'));

				return;
			}
		}
*/  
    }

    /**
     * {@inheritDoc}
     */
    public function supports($request)
    {
        return
            $request instanceof Capture &&
            $request->getModel() instanceof \ArrayAccess
        ; 
    }
}
