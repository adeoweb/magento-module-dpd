<?php

namespace AdeoWeb\Dpd\Model\Config\Source;

use AdeoWeb\Dpd\Helper\Config;
use Magento\Framework\Option\ArrayInterface;

class PageFormat implements ArrayInterface
{
    /**
     * @var Config
     */
    private $carrierConfig;

    public function __construct(Config $carrierConfig)
    {
        $this->carrierConfig = $carrierConfig;
    }

    /**
     * {@inheritDoc}
     */
    public function toOptionArray()
    {
        $options = $this->carrierConfig->getCode(Config::TYPE_AVAILABLE_PAGE_FORMATS);

        if (empty($options)) {
            return [];
        }

        $result = [];

        foreach ($options as $key => $option) {
            $result[] = [
                'value' => $key,
                'label' => $option
            ];
        }

        return $result;
    }
}