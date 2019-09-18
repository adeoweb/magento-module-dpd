<?php

namespace AdeoWeb\Dpd\Model;

use AdeoWeb\Dpd\Api\DeliveryTimeManagementInterface;
use AdeoWeb\Dpd\Helper\Config;

class DeliveryTimeManagement implements DeliveryTimeManagementInterface
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
     * @param string $city
     * @return mixed
     * @throws \Exception
     */
    public function calculate($city)
    {
        $city = \ucfirst($city);

        $deliveryTimeConfiguration = $this->carrierConfig->getCode(Config::TYPE_CLASSIC_DELIVERY_TIME_CITY, $city);

        if (!$deliveryTimeConfiguration) {
            return [];
        }

        $result = [];

        foreach ($deliveryTimeConfiguration as $item) {
            $deliveryTime = $this->carrierConfig->getCode(Config::TYPE_CLASSIC_DELIVERY_TIME, $item);

            if (!$deliveryTime) {
                throw new \Exception('Invalid configuration');
            }

            $result[] = ['value' => $item, 'label' => $deliveryTime];
        }

        return $result;
    }
}
