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

use Doctrine\Common\Persistence\ObjectManager;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\Request\Notify;
use Payum\Core\Request\Sync;
use SM\Factory\FactoryInterface;
use Sylius\Bundle\PayumBundle\Payum\Action\AbstractPaymentStateAwareAction;
use Sylius\Bundle\PayumBundle\Payum\Request\GetStatus;
use Sylius\Component\Payment\Model\PaymentInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class NotifyOrderAction extends AbstractPaymentStateAwareAction
{
    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * @param EventDispatcherInterface $eventDispatcher
     * @param ObjectManager            $objectManager
     * @param FactoryInterface         $factory
     */
    public function __construct(EventDispatcherInterface $eventDispatcher, ObjectManager $objectManager, FactoryInterface $factory)
    {
        parent::__construct($factory);

        $this->eventDispatcher = $eventDispatcher;
        $this->objectManager   = $objectManager;
    }

    /**
     * {@inheritDoc}
     *
     * @param $request Notify
     */
    public function execute($request)
    {
        if (!$this->supports($request)) {
            throw RequestNotSupportedException::createActionNotSupported($this, $request);
        }

        /** @var $payment PaymentInterface */
        $payment = $request->getModel();

        $this->payment->execute(new Sync($payment));

        $status = new GetStatus($payment);
        $this->payment->execute($status);

        $nextState = $status->getValue();

        $this->updatePaymentState($payment, $nextState);

        $this->objectManager->flush();
    }

    /**
     * {@inheritDoc}
     */
    public function supports($request)
    {
        return
            $request instanceof Notify &&
            $request->getToken() &&
            $request->getModel() instanceof PaymentInterface
        ;
    }
}
