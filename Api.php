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

use Eki\Payum\Nganluong\Api\ApiInterface;
use Eki\Payum\Nganluong\Api\AbstractApi;
use Eki\Payum\Nganluong\Api\Errors;
use Eki\Payum\Nganluong\Api\ApiException;

use Buzz\Client\ClientInterface;
use Buzz\Message\Form\FormRequest;
use Eki\Payum\Nganluong\Bridge\Buzz\Response;
use Payum\Core\Bridge\Buzz\ClientFactory;
use Payum\Core\Exception\Http\HttpException;
use Payum\Core\Exception\InvalidArgumentException;
use Payum\Core\Exception\RuntimeException;

use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;

class Api extends AbstractApi implements LoggerAwareInterface
{
    const VERSION = '3.1';

	/**
	* 
	* @var Buzz\Client\ClientInterface
	* 
	*/
    protected $client;

	/**
	* 
	* @var Psr\Log\LoggerInterface
	* 
	*/
	private $logger;

	/**
	* 
	* @var array
	* 
	*/
    protected $options = array(
        'merchant_id' => null,
        'merchant_password' => null,
        'receiver_email' => null,
        'sandbox' => null,
        'sandbox_url' => null,
        'return_url' => null,
        'cancel_url' => null,
    );

	public function setLogger(LoggerInterface $logger)
	{
		$this->logger = $logger;
	}

    /**
     * @param array $options
     * @param ClientInterface|null $client
     */
    public function __construct(array $options, ClientInterface $client = null)
    {
        $this->options = array_replace($this->options, $options);

        if (true == empty($this->options['merchant_id'])) {
            throw new \InvalidArgumentException('The merchant id option must be set.');
        }
        if (true == empty($this->options['merchant_password'])) {
            throw new \InvalidArgumentException('The merchant password option must be set.');
        }
        if (true == empty($this->options['receiver_email'])) {
            throw new \InvalidArgumentException('The receiver email as NL account option must be set.');
        }
        if (false == is_bool($this->options['sandbox'])) {
            throw new \InvalidArgumentException('The boolean sandbox option must be set.');
        }
        
        $this->client = $client ?: ClientFactory::createCurl();
    }

	/**
	* @inheritdoc 
	*/
	public function getVersion()
	{
		return self::VERSION;
	}

	/**
	* @inheritdoc 
	*/
	public function checkFields(array $fields)
	{
		$this->checkVersion($fields);
		$this->checkAuthorize($fields);
	}
	
	/**
	* Check version
	* @internal: called by checkFields function 
	* 
	* @param array $fields
	* @throw ApiException 
	*/
	private function checkVersion(array $fields)
	{
		if ( false === $fields['version'] || $fields['version'] !== $this->getVersion() )
		{
			throw new ApiException(Errors::ERRCODE_VERSION_WRONG);
		}
	}

	/**
	* Check ahthorize
	* @internal: called by checkFields function 
	* 
	* @param array $fields
	* @throw ApiException 
	*/
	private function checkAuthorize(array $fields)
	{
		if ( false === $fields['merchant_id'] )
		{
			throw new ApiException(Errors::ERRCODE_MERCHANT_ID_INVALID);
		}
		if ( false === $fields['merchant_password'] )
		{
			throw new ApiException(Errors::ERRCODE_MERCHANT_PASSWORD_INVALID);
		}
		if ( false === $fields['receiver_email'] )
		{
			throw new ApiException(Errors::ERRCODE_MERCHANT_EMAIL_INVALID);
		}
	}

	/**
	* @inheritdoc
	*/
	protected function getSupportedFunctions()
	{
		return array(
			'SetExpressCheckout' => 'setExpressCheckout',
			'GetTransactionDetail' => 'getTransactionDetails'
		);
	}
	
	/**
	* Wrapper of setExpressCheckout function
	* 
	* @param array $fields
	* 
	*/
    public function setExpressCheckout(array $fields)
	{
		return $this->doFunction($fields + array('function'=>'SetExpressCheckout'));
	}

    /**
     * Require: 
     *
     * @param array $fields
     *
     * @return array
     */
    protected function doFunction_setExpressCheckout(array $fields)
    {
        $request = new FormRequest;
        $request->setFields($fields);

        if (!isset($fields['return_url'])) {
            if (false == $this->options['return_url']) {
                throw new RuntimeException('The return_url must be set either to FormRequest or to options.');
            }

            $request->setField('return_url', $this->options['return_url']);
        }

        if (!isset($fields['cancel_url'])) {
            if (false == $this->options['cancel_url']) {
                throw new RuntimeException('The cancel_url must be set either to FormRequest or to options.');
            }

            $request->setField('cancel_url', $this->options['cancel_url']);
        }

        $request->setField('function', 'SetExpressCheckout');

        $this->addVersionField($request);
        $this->addAuthorizeFields($request);

        return $this->doRequest($request);
    }

	/**
	* Wrapper of getTransactionDetails function
	* 
	* @param array $fields
	* 
	*/
    public function getTransactionDetails(array $fields)
	{
		return $this->doFunction($fields + array('function'=>'GetTransactionDetail'));
	}
	
    /**
     * Require: token
     *
     * @param array $fields
     *
     * @return array
     */
    protected function doFunction_getTransactionDetails(array $fields)
    {
        if (!isset($fields['token'])) {
            throw new RuntimeException('The token must be set.');
        }

        $request = new FormRequest;
        $request->setFields($fields);

        $request->setField('function', 'GetTransactionDetail');

        $this->addVersionField($request);
        $this->addAuthorizeFields($request);

        return $this->doRequest($request);
    }

    /**
     * @param FormRequest $request
     *
     * @throws HttpException
     *
     * @return array
     */
    protected function doRequest(FormRequest $request)
    {
        $request->setMethod('POST');
        $request->fromUrl($this->getApiEndpoint());

        $this->client->send($request, $response = new Response);

        if (false == $response->isSuccessful()) 
		{
            throw HttpException::factory($request, $response);
        }
		else
		{
			return $response->toArray();
		}
    }

    /**
     * @return string
     */
    protected function getApiEndpoint()
    {
        return 
			$this->options['sandbox'] === true						          ?
            (
				!isset($this->options['sandbox_url'])                      ? 
				'https://www.nganluong.vn/checkout.api.nganluong.post.php' : 
				$this->options['sandbox_url']                              
			)                                                                 :       
            'https://www.nganluong.vn/checkout.api.nganluong.post.php' 		  ;
    }

    /**
     * @param FormRequest $request
     */
    protected function addAuthorizeFields(FormRequest $request)
    {
        $request->setField('merchant_id', $this->options['merchant_id']);
        $request->setField('merchant_password', md5($this->options['merchant_password']));
        $request->setField('receiver_email', $this->options['receiver_email']);
    }

    /**
     * @param FormRequest $request
     */
    protected function addVersionField(FormRequest $request)
    {
        $request->setField('version', self::VERSION);
    }
	
	public function getErrorMessages() 
	{
		return Errors::ErrorMessages();
	}
	
	public function getErrorMessage($code) 
	{
		$messages = Errors::ErrorMessages();
		return $messages[$code];
	}
}