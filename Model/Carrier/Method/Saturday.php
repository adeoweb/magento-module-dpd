<?php

namespace AdeoWeb\Dpd\Model\Carrier\Method;

use AdeoWeb\Dpd\Model\Carrier\MethodInterface;

/**
 * @codeCoverageIgnore
 */
class Saturday extends AbstractMethod implements MethodInterface
{
    const DPD_SERVICE = 'D-B2C-SAT';
    const CODE = 'saturday';

    protected $code = self::CODE;
}
