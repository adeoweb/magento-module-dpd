<?php

namespace AdeoWeb\Dpd\Helper\Config;

/**
 * Compatibility between Magento versions
 */
class Serializer
{
    /**
     * @param $value
     * @return array
     */
    public static function unserialize($value)
    {
        if (empty($value)) {
            return [];
        }

        if (self::isJson($value)) {
            return \json_decode($value, true);
        }

        return \unserialize($value);
    }

    /**
     * @param $value
     * @return bool
     */
    public static function isJson($value)
    {
        \json_decode($value, true);

        if (\json_last_error() !== JSON_ERROR_NONE) {
            return false;
        }

        return true;
    }
}