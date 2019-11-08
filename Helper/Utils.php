<?php

declare(strict_types=1);

namespace AdeoWeb\Dpd\Helper;

class Utils
{
    const PATTERN_NON_DIGITS = '/[^\d+]/';

    /**
     * @param $postcode
     * @return string|null
     */
    public function formatPostcode($postcode)
    {
        return preg_replace(self::PATTERN_NON_DIGITS, '', $postcode);
    }
}
