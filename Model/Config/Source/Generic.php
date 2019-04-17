<?php

namespace AdeoWeb\Dpd\Model\Config\Source;

use AdeoWeb\Dpd\Helper\Config;
use Magento\Shipping\Model\Carrier\Source\GenericInterface;

class Generic implements GenericInterface
{
    /**
     * @var Config
     */
    protected $carrierConfig;

    /**
     * @var string
     */
    protected $code = '';

    public function __construct(Config $carrierConfig)
    {
        $this->carrierConfig = $carrierConfig;
    }

    /**
     * {@inheritDoc}
     */
    public function toOptionArray()
    {
        $configData = $this->carrierConfig->getCode($this->code);

        $result = [];

        foreach ($configData as $code => $title) {
            $result[] = ['value' => $code, 'label' => __($title)];
        }

        return $result;
    }
}