<?php
namespace Eki\Payum\Nganluong\Tests\Request\Api;

class SetExpressCheckoutTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function shouldBeSubClassOfGeneric()
    {
        $rc = new \ReflectionClass('Eki\Payum\Nganluong\Request\Api\SetExpressCheckout');

        $this->assertTrue($rc->isSubclassOf('Payum\Core\Request\Generic'));
    }
}
