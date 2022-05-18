<?php

namespace AdeoWeb\Dpd\Api;

interface DeliveryTimeManagementInterface
{
    /**
     * Public method
     *
     * @param string $city
     * @return mixed
     * @throws \Exception
     */
    public function calculate($city);
}
