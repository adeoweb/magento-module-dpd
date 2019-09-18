<?php

namespace AdeoWeb\Dpd\Model\Carrier;

interface ValidatorInterface
{
    /**
     * @param array $context
     * @return boolean
     */
    public function validate(array $context);
}
