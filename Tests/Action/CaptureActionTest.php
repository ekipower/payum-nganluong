<?php
namespace Eki\Payum\Nganluong\Tests\Action;

use Eki\Payum\Nganluong\Action\CaptureAction;
use Eki\Payum\Nganluong\Api;
use Eki\Payum\Nganluong\Request\Api\SetExpressCheckout;
use Eki\Payum\Nganluong\Api\Errors;

use Payum\Core\Model\Token;
use Payum\Core\Request\Capture;
use Payum\Core\Tests\GenericActionTest;

class CaptureActionTest extends GenericActionTest
{
    protected $requestClass = 'Payum\Core\Request\Capture';

    protected $actionClass = 'Eki\Payum\Nganluong\Action\CaptureAction';

    /**
     * @test
     */
    public function shouldBeSubClassOfPaymentAwareAction()
    {
        $rc = new \ReflectionClass('Eki\Payum\Nganluong\Action\CaptureAction');
        
        $this->assertTrue($rc->isSubclassOf('Payum\Core\Action\PaymentAwareAction'));
    }

    /**
     * @test
     */
    public function shouldNotExecuteAnythingIfSetExpressCheckoutActionFails()
    {
        $paymentMock = $this->createPaymentMock();
        $paymentMock
            ->expects($this->once())
            ->method('execute')
            ->with($this->isInstanceOf('Eki\Payum\Nganluong\Request\Api\SetExpressCheckout'))
            ->will($this->returnCallback(function(SetExpressCheckout $request) {
                $model = $request->getModel();

                $model['error_code'] = Errors::ERRCODE_UNKNOWN;
            }))
        ;

        $action = new CaptureAction();
        $action->setPayment($paymentMock);

        $action->execute(new Capture(array()));
    }

    /**
     * @test
     */
    public function shouldSetTokenTargetUrlAsReturnUrlIfCapturePassedWithToken()
    {
        $testCase = $this;

        $expectedTargetUrl = 'theTargetUrl';

        $token = new Token;
        $token->setTargetUrl($expectedTargetUrl);
        $token->setDetails(array());

        $paymentMock = $this->createPaymentMock();
        $paymentMock
            ->expects($this->at(0))
            ->method('execute')
            ->with($this->isInstanceOf('Eki\Payum\Nganluong\Request\Api\SetExpressCheckout'))
            ->will($this->returnCallback(function($request) use ($testCase, $expectedTargetUrl) {
                $model = $request->getModel();

                $testCase->assertEquals($expectedTargetUrl, $model['return_url']);
            }))
        ;

        $action = new CaptureAction();
        $action->setPayment($paymentMock);

        $request = new Capture($token);
        $request->setModel(array());

        $action->execute($request);
    }

    /**
     * @test
     */
    public function shouldSetTokenTargetUrlAsCancelUrlIfCapturePassedWithToken()
    {
        $testCase = $this;

        $expectedCancelUrl = 'theCancelUrl';

        $token = new Token;
        $token->setTargetUrl($expectedCancelUrl);
        $token->setDetails(array());

        $paymentMock = $this->createPaymentMock();
        $paymentMock
            ->expects($this->at(0))
            ->method('execute')
            ->with($this->isInstanceOf('Eki\Payum\Nganluong\Request\Api\SetExpressCheckout'))
            ->will($this->returnCallback(function($request) use ($testCase, $expectedCancelUrl) {
                $model = $request->getModel();

                $testCase->assertEquals($expectedCancelUrl, $model['cancel_url']);
            }))
        ;

        $action = new CaptureAction();
        $action->setPayment($paymentMock);

        $request = new Capture($token);
        $request->setModel(array());

        $action->execute($request);
    }

    /**
     * @test
     */
    public function shouldNotRequestSetExpressCheckoutActionIfTokenSetInModel()
    {
        $paymentMock = $this->createPaymentMock();
        $paymentMock
            ->expects($this->at(0))
            ->method('execute')
            ->with($this->isInstanceOf('Payum\Core\Request\Sync'))
        ;

        $action = new CaptureAction();
        $action->setPayment($paymentMock);

        $action->execute(new Capture(array(
            'token' => 'aToken'
        )));
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\Payum\Core\PaymentInterface
     */
    protected function createPaymentMock()
    {
        return $this->getMock('Payum\Core\PaymentInterface');
    }
}
