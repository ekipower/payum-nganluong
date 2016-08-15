<?php
/**
 * This file is part of the EkiPayumNganluong package.
 *
 * (c) EkiPower <http://ekipower.github.com/>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */ 

namespace Eki\Payum\Nganluong\Request\ATMOnline;

use Eki\Payum\Nganluong\Model\BankInterface;

use Payum\Core\Exception\LogicException;

class DetermineBank
{
    /**
     * @var BankInterface
     */
    protected $bank;

    /**
     * @param BankInterface $bank
     */
    public function set(BankInterface $bank)
    {
        $this->bank = $bank;
    }

    /**
     * @return BankInterface
     */
    public function determine()
    {
        if (false == $this->bank) {
            throw new LogicException('Bank could not be determined. It has to be set before determination.');
        }

        return $this->bank;
    }
}
