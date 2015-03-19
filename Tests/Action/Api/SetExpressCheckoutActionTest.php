<?php
namespace Eki\Payum\Nganluong\Tests\Action\Api;

use Eki\Payum\Nganluong\Action\Api\SetExpressCheckoutAction;
use Eki\Payum\Nganluong\Request\Api\SetExpressCheckout;

class SetExpressCheckoutActionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function shouldBeSubClassOfBaseApiAwareAction()
    {
        $rc = new \ReflectionClass('Eki\Payum\Nganluong\Action\Api\SetExpressCheckoutAction');

        $this->assertTrue($rc->isSubclassOf('Eki\Payum\Nganluong\Action\Api\BaseApiAwareAction'));
    }

    /**
     * @test
     */
    public function couldBeConstructedWithoutAnyArguments()
    {
        new SetExpressCheckoutAction();
    }

    /**
     * @test
     */
    public function shouldSupportSetExpressCheckoutRequestAndArrayAccessAsModel()
    {
        $action = new SetExpressCheckoutAction();

        $request = new SetExpressCheckout($this->getMock('ArrayAccess'));

        $this->assertTrue($action->supports($request));
    }

    /**
     * @test
     */
    public function shouldNotSupportAnythingNotSetExpressCheckoutRequest()
    {
        $action = new SetExpressCheckoutAction();

        $this->assertFalse($action->supports(new \stdClass()));
    }

    /**
     * @test
     *
     * @expectedException \Payum\Core\Exception\RequestNotSupportedException
     */
    public function throwIfNotSupportedRequestGivenAsArgumentForExecute()
    {
        $action = new SetExpressCheckoutAction();

        $action->execute(new \stdClass());
    }

    /**
     * @test
     *
     * @expectedException \Payum\Core\Exception\LogicException
     * @expectedExceptionMessage The PAYMENTREQUEST_0_AMT must be set.
     */
    public function throwIfModelNotHavePaymentAmountSet()
    {
        $action = new SetExpressCheckoutAction();

        $request = new SetExpressCheckout(new \ArrayObject());

        $action->execute($request);
    }

    /**
     * @test
     */
    public function shouldCallApiGetExpressCheckoutDetailsMethodWithExpectedRequiredArguments()
    {
        $testCase = $this;

        $expectedAmount = 154.23;

        $apiMock = $this->createApiMock();
        $apiMock
            ->expects($this->once())
            ->method('setExpressCheckout')
            ->will($this->returnCallback(function(array $fields) use ($testCase, $expectedAmount) {
                $testCase->assertArrayHasKey('total_amount', $fields);
                $testCase->assertEquals($expectedAmount, $fields['total_amount']);

                return array();
            }))
        ;

        $action = new SetExpressCheckoutAction($apiMock);
        $action->setApi($apiMock);

        $request = new SetExpressCheckout(array(
            'total_amount' => $expectedAmount
        ));

        $action->execute($request);
    }

    /**
     * @test
     */
    public function shouldCallApiDoExpressCheckoutMethodAndUpdateInstructionFromResponseOnSuccess()
    {
        $apiMock = $this->createApiMock();
        $apiMock
            ->expects($this->once())
            ->method('setExpressCheckout')
            ->will($this->returnCallback(function() {
                return array(
                    'buyer_fullname'=> 'FullName',
                    'buyer_email' => 'the@example.com'
                );
            }))
        ;

        $action = new SetExpressCheckoutAction();
        $action->setApi($apiMock);

        $request = new SetExpressCheckout(array(
            'total_amount' => $expectedAmount = 154.23
        ));

        $action->execute($request);

        $model = $request->getModel();

        $this->assertEquals('FullName', $model['buyer_fullname']);
        $this->assertEquals('the@example.com', $model['buyer_email']);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\Payum\Paypal\ExpressCheckout\Nvp\Api
     */
    protected function createApiMock()
    {
        return $this->getMock('Eki\Payum\Nganluong\Api', array(), array(), '', false);
    }
}
