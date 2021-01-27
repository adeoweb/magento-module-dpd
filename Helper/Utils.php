<?php

declare(strict_types=1);

namespace AdeoWeb\Dpd\Helper;

use Zend_Uri_Http;

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

    /**
     * @param $url
     * @return string
     * @throws \Zend_Uri_Exception
     */
    public function getTldFromUrl($url): string
    {
        $url = Zend_Uri_Http::fromString($url);

        $array = explode('.', $url->getHost());

        return (string)end($array);
    }
}
