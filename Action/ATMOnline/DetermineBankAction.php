<?php
/**
 * This file is part of the EkiPayumNganluongBundle package.
 *
 * (c) EkiPower <http://ekipower.github.com/>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */ 

namespace Eki\Payum\Nganluong\Action\ATMOnline;

use Eki\Payum\Nganluong\Model\BankInterface;
use Eki\Payum\Nganluong\Request\ATMOnline\DetermineBank;

use Payum\Core\Action\PaymentAwareAction;
use Payum\Core\Bridge\Symfony\Reply\HttpResponse;
use Payum\Core\Exception\LogicException;
use Payum\Core\Exception\RequestNotSupportedException;

use Payum\Core\Request\RenderTemplate;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DetermineBankAction extends PaymentAwareAction
{
    /**
     * @var FormFactoryInterface
     */
    protected $formFactory;

    /**
     * @var Request
     */
    protected $httpRequest;

    /**
     * @var string
     */
    protected $templateName;

    /**
     * @var string
     */
    protected $formName;

    /**
     * @param FormFactoryInterface $formFactory
     * @param string $templateName
     * @param string $formName
     */
    public function __construct(FormFactoryInterface $formFactory, $templateName, $formName = null)
    {
        $this->formFactory = $formFactory;
        $this->templateName = $templateName;
		$this->formName = $formName;
    }

    /**
     * @param Request $request
     */
    public function setRequest(Request $request = null)
    {
        $this->httpRequest = $request;
    }

    /**
     * {@inheritDoc}
     */
    public function execute($request)
    {
        /** @var $request DetermineBank */
        if (!$this->supports($request)) {
            throw RequestNotSupportedException::createActionNotSupported($this, $request);
        }
        if (!$this->httpRequest) {
            throw new LogicException('The action can be run only when http request is set.');
        }

        $form = $this->createBankListForm();

        $form->handleRequest($this->httpRequest);
        if ($form->isSubmitted()) {
            /** @var BankInterface $bank */
            $bank = $form->getData();

            if ($form->isValid()) {
                $request->set($bank);

                return;
             }
        }

        $renderTemplate = new RenderTemplate($this->templateName, array(
            'form' => $form->createView()
        ));
        $this->payment->execute($renderTemplate);

        throw new HttpResponse(new Response($renderTemplate->getResult(), 200, array(
            'Cache-Control' => 'no-store, no-cache, max-age=0, post-check=0, pre-check=0',
            'Pragma' => 'no-cache',
        )));
    }

    /**
     * {@inheritDoc}
     */
    public function supports($request)
    {
        return $request instanceof DetermineBank;
    }

    /**
     * @return FormInterface
     */
    protected function createBankListForm()
    {
        return $this->formFactory->create( null !== $this->formName ? $this->formName : 'eki_nganluong_bank_list' );
    }
}