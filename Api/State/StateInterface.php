<?php
/**
 * This file is part of the EkiPayumNganluong package.
 *
 * (c) EkiPower <http://ekipower.github.com/>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */ 

namespace Eki\Payum\Nganluong\Api\State;

interface StateInterface
{
    const STATE_WAITING = 'waiting_for_reply';

    const STATE_REPLIED = 'replied';

    const STATE_NOTIFIED = 'notified';

    const STATE_CONFIRMED = 'confirmed';

    const STATE_ERROR = 'error';
}
