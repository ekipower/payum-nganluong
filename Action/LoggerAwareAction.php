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

use Eki\Payum\Nganluong\Request\Log;

use Payum\Core\Action\ActionInterface;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;

class LoggerAwareAction implements ActionInterface, LoggerAwareInterface
{
	/**
	* 
	* @var Psr\Log\LoggerInterface
	* 
	*/
    protected $logger;

    /**
     * {@inheritDoc}
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * {@inheritDoc}
     */
    public function execute($request)
    {
        if ($this->logger) {
			$action = $request->getType();
            $this->logger->$action($request->getMessage());
        }
    }

    /**
     * {@inheritDoc}
     */
    public function supports($request)
    {
        return $request instanceof Log;
    }
}