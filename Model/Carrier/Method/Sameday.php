<?php

namespace AdeoWeb\Dpd\Model\Carrier\Method;

use AdeoWeb\Dpd\Model\Carrier\MethodInterface;

/**
 * Class Saturday
 * @codeCoverageIgnore
 */
class Sameday extends AbstractMethod implements MethodInterface
{
    const DPD_SERVICE = 'SD';
    const CODE = 'sameday';

    protected $code = self::CODE;
}
