<?php

namespace AdeoWeb\Dpd\Model\Config\Source;

class Generic implements \Magento\Shipping\Model\Carrier\Source\GenericInterface
{
    /**
     * @var \AdeoWeb\Dpd\Helper\Config
     */
    protected $carrierConfig;

    /**
     * @var string
     */
    protected $code = '';

    public function __construct(\AdeoWeb\Dpd\Helper\Config $carrierConfig)
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