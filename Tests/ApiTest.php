<?php
namespace Eki\Payum\Nganluong\Tests;

use Eki\Payum\Nganluong\Api;

use Buzz\Message\Response;
use Buzz\Message\Form\FormRequest;

class ApiTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function couldBeConstructedWithOptionsOnly()
    {
        $api = new Api(array(
            'merchant_id' => 'an_id',
            'merchant_password' => 'a_password',
            'receiver_email' => 'an_email',
            'sandbox' => true,
        ));

        $this->assertAttributeInstanceOf('Buzz\Client\Curl', 'client', $api);
    }

    /**
     * @test
     */
    public function couldBeConstructedWithOptionsAndBuzzClient()
    {
        $client = $this->createClientMock();

        $api = new Api(array(
            'merchant_id' => 'an_id',
            'merchant_password' => 'a_password',
            'receiver_email' => 'an_email',
            'sandbox' => true,
        ), $client);

        $this->assertAttributeSame($client, 'client', $api);
    }

    /**
     * @test
     * 
     * @expectedException \Payum\Core\Exception\InvalidArgumentException
     * @expectedExceptionMessage The username option must be set.
     */
    public function throwIfMerchantIdOptionNotSetInConstructor()
    {
        new Api(array());
    }

    /**
     * @test
     *
     * @expectedException \Payum\Core\Exception\InvalidArgumentException
     * @expectedExceptionMessage The password option must be set.
     */
    public function throwIfMerchantPasswordOptionNotSetInConstructor()
    {
        new Api(array(
            'merchant_id' => 'an_id'
        ));
    }

    /**
     * @test
     *
     * @expectedException \Payum\Core\Exception\InvalidArgumentException
     * @expectedExceptionMessage The signature option must be set.
     */
    public function throwIfReceiverEmailOptionNotSetInConstructor()
    {
        new Api(array(
            'merchant_id' => 'an_id',
            'merchant_password' => 'a_password'
        ));
    }

    /**
     * @test
     *
     * @expectedException \Payum\Core\Exception\InvalidArgumentException
     * @expectedExceptionMessage The boolean sandbox option must be set.
     */
    public function throwIfSandboxOptionNotSetInConstructor()
    {
        new Api(array(
            'merchant_id' => 'an_id',
            'merchant_password' => 'a_password',
            'receiver_email' => 'an_email',
        ));
    }

    /**
     * @test
     * 
     * @expectedException \Payum\Core\Exception\RuntimeException
     * @expectedExceptionMessage The return_url must be set either to FormRequest or to options.
     */
    public function throwIfReturnUrlNeitherSetToFormRequestNorToOptions()
    {
        $api = new Api(array(
            'merchant_id' => 'an_id',
            'merchant_password' => 'a_password',
            'receiver_email' => 'an_email',
            'sandbox' => true,
        ));
        
        $api->setExpressCheckout(array());
    }

    /**
     * @test
     */
    public function shouldUseReturnUrlSetInFormRequest()
    {
        $api = new Api(array(
            'merchant_id' => 'an_id',
            'merchant_password' => 'a_password',
            'receiver_email' => 'an_email',
            'sandbox' => true,
            'return_url' => 'optionReturnUrl',
            'cancel_url' => 'optionCancelUrl'
        ), $this->createSuccessClientStub());
        
        $result = $api->setExpressCheckout(array('return_url' => 'formRequestReturnUrl'));
        
        $this->assertEquals('formRequestReturnUrl', $result['return_url']);
    }

    /**
     * @test
     */
    public function shouldUseReturnUrlSetInOptions()
    {
        $api = new Api(array(
            'merchant_id' => 'an_id',
            'merchant_password' => 'a_password',
            'receiver_email' => 'an_email',
            'sandbox' => true,
            'return_url' => 'optionReturnUrl',
            'cancel_url' => 'optionCancelUrl'
        ), $this->createSuccessClientStub());

        $result = $api->setExpressCheckout(array());

        $this->assertEquals('optionReturnUrl', $result['return_url']);
    }

    /**
     * @test
     *
     * @expectedException \Payum\Core\Exception\RuntimeException
     * @expectedExceptionMessage The return_url must be set either to FormRequest or to options.
     */
    public function throwIfCancelUrlNeitherSetToFormRequestNorToOptions()
    {
        $api = new Api(array(
            'merchant_id' => 'an_id',
            'merchant_password' => 'a_password',
            'receiver_email' => 'an_email',
            'sandbox' => true,
        ), $this->createClientMock());

        $api->setExpressCheckout(array());
    }

    /**
     * @test
     */
    public function shouldUseCancelUrlSetInFormRequest()
    {
        $api = new Api(array(
            'merchant_id' => 'an_id',
            'merchant_password' => 'a_password',
            'receiver_email' => 'an_email',
            'sandbox' => true,
            'return_url' => 'optionReturnUrl',
            'cancel_url' => 'optionCancelUrl'
        ), $this->createSuccessClientStub());

        $result = $api->setExpressCheckout(array('cancel_url' => 'formRequestCancelUrl'));

        $this->assertEquals('formRequestCancelUrl', $result['cancel_url']);
    }

    /**
     * @test
     */
    public function shouldUseCancelUrlSetInOptions()
    {
        $api = new Api(array(
            'merchant_id' => 'an_id',
            'merchant_password' => 'a_password',
            'receiver_email' => 'an_email',
            'sandbox' => true,
            'return_url' => 'optionReturnUrl',
            'cancel_url' => 'optionCancelUrl'
        ), $this->createSuccessClientStub());

        $result = $api->setExpressCheckout(array());

        $this->assertEquals('optionCancelUrl', $result['cancel_url']);
    }

    /**
     * @test
     */
    public function shouldAddMethodOnSetExpressCheckoutCall()
    {
        $api = new Api(array(
            'merchant_id' => 'an_id',
            'merchant_password' => 'a_password',
            'receiver_email' => 'an_email',
            'sandbox' => true,
            'return_url' => 'optionReturnUrl',
            'cancel_url' => 'optionCancelUrl'
        ), $this->createSuccessClientStub());

        $result = $api->setExpressCheckout(array());

        $this->assertArrayHasKey('function', $result);
        $this->assertEquals('SetExpressCheckout', $result['function']);
    }

    /**
     * @test
     */
    public function shouldAddAuthorizeFieldsOnSetExpressCheckoutCall()
    {
        $api = new Api(array(
            'merchant_id' => 'an_id',
            'merchant_password' => 'a_password',
            'receiver_email' => 'an_email',
            'sandbox' => true,
            'return_url' => 'optionReturnUrl',
            'cancel_url' => 'optionCancelUrl'
        ), $this->createSuccessClientStub());

        $result = $api->setExpressCheckout(array());

        $this->assertArrayHasKey('merchant_id', $result);
        $this->assertEquals('an_id', $result['merchant_id']);

        $this->assertArrayHasKey('merchant_password', $result);
        $this->assertEquals('a_password', $result['merchant_password']);

        $this->assertArrayHasKey('receiver_email', $result);
        $this->assertEquals('an_email', $result['receiver_email']);
    }

    /**
     * @test
     */
    public function shouldAddVersionOnSetExpressCheckoutCall()
    {
        $api = new Api(array(
            'merchant_id' => 'an_id',
            'merchant_password' => 'a_password',
            'receiver_email' => 'an_email',
            'sandbox' => true,
            'return_url' => 'optionReturnUrl',
            'cancel_url' => 'optionCancelUrl'
        ), $this->createSuccessClientStub());

        $result = $api->setExpressCheckout(array());

        $this->assertArrayHasKey('version', $result);
        $this->assertEquals(Api::VERSION, $result['version']);
    }

    /**
     * @test
     */
    public function shouldUseRealApiEndpointIfSandboxFalse()
    {
        $testCase = $this;

        $clientMock = $this->createClientMock();
        $clientMock
            ->expects($this->once())
            ->method('send')
            ->will($this->returnCallback(function(FormRequest $request, Response $response) use ($testCase) {
                $testCase->assertEquals('https://www.nganluong.vn/checkout.api.nganluong.post.php', $request->getUrl());

                $response->setHeaders(array('HTTP/1.1 200 OK'));
                $response->setContent('ACK=Success');

                $response->setContent(http_build_query($request->getFields()));
            }))
        ;

        $api = new Api(array(
            'merchant_id' => 'an_id',
            'merchant_password' => 'a_password',
            'receiver_email' => 'an_email',
            'sandbox' => false,
            'return_url' => 'optionReturnUrl',
            'cancel_url' => 'optionCancelUrl'
        ), $clientMock);

        $api->setExpressCheckout(array());
    }

    /**
     * @test
     */
    public function shouldUseSandboxApiEndpointIfSandboxTrue()
    {
        $testCase = $this;

        $clientMock = $this->createClientMock();
        $clientMock
            ->expects($this->once())
            ->method('send')
            ->will($this->returnCallback(function(FormRequest $request, Response $response) use ($testCase) {
                $testCase->assertEquals('https://www.nganluong.vn/checkout.api.nganluong.post.php', $request->getUrl());

                $response->setHeaders(array('HTTP/1.1 200 OK'));
                $response->setContent('ACK=Success');

                $response->setContent(http_build_query($request->getFields()));
            }))
        ;

        $api = new Api(array(
            'merchant_id' => 'an_id',
            'merchant_password' => 'a_password',
            'receiver_email' => 'an_email',
            'sandbox' => true,
            'return_url' => 'optionReturnUrl',
            'cancel_url' => 'optionCancelUrl'
        ), $clientMock);

        $api->setExpressCheckout(array());
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\Buzz\Client\ClientInterface
     */
    protected function createClientMock()
    {
        return $this->getMock('Buzz\Client\ClientInterface');
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\Buzz\Client\ClientInterface
     */
    protected function createSuccessClientStub()
    {
        $clientMock = $this->createClientMock();
        $clientMock
            ->expects($this->any())
            ->method('send')
            ->will($this->returnCallback(function(FormRequest $request, Response $response) {
                $response->setHeaders(array('HTTP/1.1 200 OK'));
                $response->setContent('ACK=Success');

                $response->setContent(http_build_query($request->getFields()));
            }))
        ;
        
        return $clientMock;
    }
}
