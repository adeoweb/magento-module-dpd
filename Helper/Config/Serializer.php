<?php

namespace AdeoWeb\Dpd\Helper\Config;

use Magento\Framework\Serialize\Serializer\Serialize;

/**
 * Compatibility between Magento versions
 */
class Serializer
{
    /**
     * @var Serializer
     */
    private $serializer;

    public function __construct(
        Serialize $serializer
    ) {
        $this->serializer = $serializer;
    }

    /**
     * @param string $value
     * @return array
     */
    public function unserialize($value)
    {
        if (empty($value)) {
            return [];
        }

        if (self::isJson($value)) {
            return \json_decode($value, true);
        }

        return $this->serializer->unserialize($value);
    }

    /**
     * @param string $value
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
